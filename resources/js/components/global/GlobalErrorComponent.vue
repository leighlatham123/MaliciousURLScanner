<template>
    <div>
        <div class="global__modal--mask">
            <div class="global__modal--wrapper-large">
                <div class="global__modal--container modal-error">
                    <div class="global__modal--header">
                        <h1 class="global__modal--header-title">Error Report</h1>
                    </div>
                    <div class="global__modal--content">
                        <div class="global__modal--body">


                            <template v-if="loadErrors === 'go'">
                                <div class="global__modal--extra">
                                    <div class="global__modal--body-field">
                                        <div class="global__modal--body-field">
                                            <label class="global__modal-title">{{googleErrors}}</label>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-else-if="loadErrors === 'sc'">
                                <div class="global__modal--extra">
                                    <div class="global__modal--body-field">
                                        <div class="global__modal--body-field">
                                            <label class="global__modal-title">{{scanErrors}}</label>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="global__modal--extra">
                                    <div class="global__modal--body-field">
                                        <div class="global__modal--body-field">
                                            <label class="global__modal-title">{{virusTotalErrors}}</label>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                    <div class="global__modal--footer">
                        <div class="global__modal--footer-actions">
                            <input class="global__modal--footer-actions-button button-secondary"
                                @click="closeModal" type="button" name="commit" data-disable-with="Submit" value="Cancel">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'showError',
        'loadErrors',
        'googleErrors',
        'scanErrors',
        'virusTotalErrors'
    ],
    methods: {
        closeModal() {
            this.$emit('update:showError', false)
        }
    }
}
</script>
