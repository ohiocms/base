<?php

use Illuminate\Support\Facades\DB;
use Belt\Core\Behaviors\HasDisk;
use Belt\Core\Helpers\DebugHelper;
use Belt\Core\Services\Update\BaseUpdate;
use Belt\Content\Section;

/**
 * Class UpdateService
 * @package Belt\Core\Services
 */
class BeltUpdateTemplates extends BaseUpdate
{
    use HasDisk;

    /**
     * @var array
     */
    public $argumentMap = [
        'method',
    ];

    public function up()
    {
        /**
         * create
         * update
         * move
         * db
         */
        $method = $this->argument('method');
        $method = camel_case($method);
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    /**
     * @param $morphClass
     * @return string
     */
    public function getTemplateType($morphClass)
    {
        $type = in_array($morphClass, ['pages', 'categories', 'places', 'posts', 'events']) ? $morphClass : 'sections';

        return $type;
    }

    /**
     * @param $morphClass
     * @param $templateKey
     * @param $oldConfig
     * @return array
     */
    public function getNewConfig($morphClass, $templateKey, $oldConfig)
    {

        $newConfig = [
            'builder' => array_get($oldConfig, 'builder', null),
            'extends' => array_get($oldConfig, 'extends', ''),
            'path' => array_get($oldConfig, 'path', ''),
            'label' => array_get($oldConfig, 'label', ''),
            'description' => array_get($oldConfig, 'description', ''),
        ];

        $type = $this->getTemplateType($morphClass);
        if ($type == 'sections') {
            $qb = Section::where('sectionable_type', $morphClass)->where('template', $templateKey);
            foreach (['heading', 'before', 'after'] as $column) {
                $clone = clone $qb;
                $clone->where(function ($qb) use ($column) {
                    $qb->whereNotNull($column);
                    $qb->orWhere($column, '!=', '');
                });
                //$newConfig[$column] = $clone->first() ? true : false;
                $newConfig[$column] = false;
                if ($clone->first()) {
                    $newConfig[$column] = [
                        'label' => '',
                        'description' => '',
                    ];
                }
            }
        }

        $params = array_get($oldConfig, 'params', []);
        if ($params) {
            foreach ($params as $key => $values) {
                $newConfig['params'][$key] = [
                    'class' => null,
                    'type' => is_array($values) ? 'select' : 'text',
                    'options' => is_array($values) ? $values : null,
                    'label' => '',
                    'description' => '',
                    'plugin' => '',
                    'validation' => '',
                ];
            }
            asort($newConfig['params']);
        }

        return $newConfig;
    }

    public function create()
    {
        $configKey = $this->option('configKey', 'belt.templates');

        foreach (config($configKey) as $morphClass => $templates) {
            foreach ($templates as $templateKey => $config) {
                $this->__create($morphClass, $templateKey);
            }
        }
    }

    public function __create($morphClass, $templateKey)
    {
        $this->info(sprintf('re-organize %s:%s', $morphClass, $templateKey));

        $path = sprintf('config/belt/templates/%s/%s.php', $morphClass, $templateKey);
        $tmpPath = sprintf('config/belt/templates-tmp/%s/%s.php', $morphClass, $templateKey);

        $type = $this->getTemplateType($morphClass);
        if ($type == 'sections') {
            $morphClass = $morphClass == 'sections' ? 'containers' : $morphClass;
            $tmpPath = sprintf('config/belt/templates-tmp/%s/%s/%s.php', $type, $morphClass, $templateKey);
        }

        $this->disk()->copy($path, $tmpPath);

    }

    public function update()
    {
        $configKey = $this->option('configKey', 'belt.templates');

        foreach (config($configKey) as $morphClass => $templates) {
            foreach ($templates as $templateKey => $config) {
                //if ($morphClass == 'attachments' && $templateKey == 'default') {
                    $this->__update($morphClass, $templateKey, $config);
                //}
            }
        }
    }

    public function __update($morphClass, $templateKey, $oldConfig)
    {
        $this->info(sprintf('re-organize %s:%s', $morphClass, $templateKey));

        $newConfig = $this->getNewConfig($morphClass, $templateKey, $oldConfig);

        $tmpPath = sprintf('config/belt/templates-tmp/%s/%s.php', $morphClass, $templateKey);

        $type = $this->getTemplateType($morphClass);
        if ($type == 'sections') {
            $morphClass = $morphClass == 'sections' ? 'containers' : $morphClass;
            $tmpPath = sprintf('config/belt/templates-tmp/%s/%s/%s.php', $type, $morphClass, $templateKey);
        }

        $contents = sprintf("<?php\r\n\r\nreturn %s;", DebugHelper::varExportShort($newConfig));

        $this->disk()->put($tmpPath, $contents);
    }

    public function move()
    {
        $tmpPath = $this->option('new-path', 'templates-tmp');
        $tmpPath = config_path('belt/' . $tmpPath);
        if ($tmpPath && file_exists($tmpPath)) {
            $targetPath = $this->option('target-path', 'templates');
            $targetPath = config_path('belt/' . $targetPath);
            if ($targetPath) {
                if (file_exists($targetPath)) {
                    $archivedPath = "$targetPath-archived";
                    rename($targetPath, $archivedPath);
                    $this->info("moved existing path to: $archivedPath");
                }
                rename($tmpPath, $targetPath);
                $this->info("moved new path to: $targetPath");
            }
        }
    }

    public function db()
    {
        Section::unguard();

        Section::where(function ($query) {
            $query->whereNull('template');
            $query->orWhere('template', '');
        })
            ->update([
                'template' => 'default'
            ]);

        Section::where('template', 'NOT LIKE', '%.%')
            ->update([
                'template' => DB::raw("CONCAT(sections.sectionable_type, '.', sections.template)")
            ]);

        Section::whereIn('sectionable_type', ['sections', 'custom', 'menus'])
            ->update([
                'sectionable_type' => null
            ]);

        Section::where('template', 'LIKE', 'sections.%')
            ->update([
                'template' => DB::raw("REPLACE(`template`, 'sections.', 'containers.')")
            ]);

        $configKey = $this->option('configKey', 'belt.templates.sections');

//        Section::where('template', '')->update(['template' => 'default']);
//
//        foreach (config($configKey) as $morphClass => $templates) {
//            foreach ($templates as $templateKey => $config) {
//                $this->__db($morphClass, $templateKey);
//            }
//        }
//
//        Section::whereNull('sectionable_id')->update(['sectionable_type' => null]);
    }

//    public function __db($morphClass, $templateKey)
//    {
//        $this->info(sprintf('update sections db: %s %s', $morphClass, $templateKey));
//
//        $oldSectionableType = $morphClass;
//        if (in_array($oldSectionableType, ['containers'])) {
//            $oldSectionableType = 'sections';
//        }
//
//        $newTemplateKey = sprintf('%s.%s', $morphClass, $templateKey);
//        Section::whereNotNull('sectionable_type')
//            ->where('sectionable_type', $oldSectionableType)
//            ->where('template', $templateKey)->update(['template' => $newTemplateKey]);
//
//    }

}