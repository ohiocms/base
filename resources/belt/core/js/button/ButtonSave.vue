<template>
    <button class="btn btn-sm btn-primary" :disabled="form.errors.any()">
        <span v-if="form.saving"><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
        <span v-else>save</span>
    </button>
</template>

<script>
    import shared from 'belt/core/js/button/shared';

    export default {
        mixins: [shared],
    }
</script>