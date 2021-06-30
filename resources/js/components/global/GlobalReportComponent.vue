<template>
    <div>
        <div class="global__modal--mask">
            <div class="global__modal--wrapper-large">
                <div class="global__modal--container modal-report">
                    <div class="global__modal--header">
                        <h1 class="global__modal--header-title">Scan Report</h1>
                    </div>
                    <div class="global__modal--content">


                        <template v-if="loadReport === 'go'">
                            <div class="global__modal--extra">
                                <div class="global__modal--body-field">
                                    <div class="global__modal--body-field" v-if="googleThreats">
                                        <label class="global__modal-title">Found: </label>
                                        <span class="global__modal-special">{{ googleThreats }}</span>
                                        <label class="global__modal-title">issues.</label>
                                    </div>
                                    <div class="global__modal--body-field" v-else>
                                        <label class="global__modal-title">No threats were found during this
                                            scan.</label>
                                    </div>
                                </div>
                                <div class="report__wrapper">
                                    <div class="report__container">
                                        <div class="report__field" v-for="(res, key) in googleData" :key="key">
                                            <div>
                                                <label class="global__modal-text">Platform:</label><span
                                                    class="report__data">{{ res.platformType }}</span>
                                            </div>
                                            <div>
                                                <label class="global__modal-text">Threat Entry:</label><span
                                                    class="report__data">{{ res.threatEntryType }}</span>
                                            </div>
                                            <div>
                                                <label class="global__modal-text">Type:</label><span
                                                    class="report__data">{{ res.threatType }}</span>
                                            </div>
                                            <div>
                                                <label class="global__modal-text">Threat:</label><span
                                                    class="report__data"><a :href="res.threat.url"
                                                        target="_blank">Malicious Link</a></span>
                                            </div>
                                            <div>
                                                <label class="global__modal-text">Result Cache:</label><span
                                                    class="report__data">{{ res.cacheDuration }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-else-if="loadReport === 'sc'">
                        </template>

                        <template v-else>
                            <div class="global__modal--body-field">
                                <div class="global__modal--body-field" v-if="virusTotalThreats">
                                    <label class="global__modal-title">Found: </label>
                                    <span class="global__modal-special">{{ virusTotalThreats }}</span>
                                    <label class="global__modal-title">issues.</label>
                                </div>
                                <div class="global__modal--body-field" v-else>
                                    <label class="global__modal-title">No threats were found during this scan.</label>
                                </div>
                            </div>
                            <div class="report__wrapper" v-if="virusTotalThreats">
                                <div class="report__container">
                                    <table class="report__table">
                                        <thead class="report__table-head">
                                            <tr>
                                                <th class="report__table-head-text">Company</th>
                                                <th class="report__table-head-text">Detected</th>
                                                <th class="report__table-head-text">Result</th>
                                            </tr>
                                        </thead>
                                        <tbody class="report__table-body">
                                            <tr v-for="(res, key) in virusTotalArray" :key="key">
                                                <td class="report__table-data data-white">{{ res[0] }}</td>
                                                <td :class="'report__table-data ' + detectedColor(res[1].detected)">{{ res[1].detected }}</td>
                                                <td :class="'report__table-data ' + resultColor(res[1].result)">{{ res[1].result }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

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
        'showReport',
        'googleResults',
        'scanResults',
        'virusTotalResults',
        'loadReport'
    ],
    methods: {
        closeModal() {
            this.$emit('update:showReport', false)
        },
        detectedColor(bool) {
            if (bool) {
                return 'data-red'
            }

            return 'data-green'
        },
        resultColor(str) {
            if (str === 'clean site' || str === 'unrated site') {
                return 'data-green'
            }

            return 'data-red'
        }
    },
    computed: {
        googleData() {
            return this.googleResults.data.matches
        },
        googleThreats() {
            if (this.googleData) {
                return this.googleResults.data.matches.length
            }

            return null
        },
        virusTotalData() {
            return this.virusTotalResults.data
        },
        virusTotalThreats() {
            if (this.virusTotalData) {
                if (this.virusTotalData.positives > 0) {
                    return this.virusTotalData.positives
                }

                return false
            }

            return null
        },
        virusTotalArray() {
            if (this.virusTotalData) {
                return Object.entries(this.virusTotalData.scans)
            }

            return null
        }
    }
}
</script>
