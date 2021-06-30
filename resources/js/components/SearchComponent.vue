<template>
    <div class="content">
            <div class="box">
                <div class="box__container">
                    <div class="box__header">
                        <div class="box__description">
                            <div class="box__title">
                                <span class="box__text">IP & URL Scanner</span>
                            </div>
                            <div class="box__details">
                                <h1 class="box__text">How to use:</h1>
                                <p class="box__text">Enter an IP address or full URL and click scan. This scanner will attempt to check
                                    a given address against multiple well-known security databases and return the results.
                                    If you don't have anything to test, you might try one of the addresses found on this
                                    <a href="https://www.wicar.org/test-malware.html" target="_blank">site</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="box__body">
                        <form @submit.prevent="submitSearch" class="box__form" id="search_form" action="check_url" method="post" accept-charset="UTF-8">
                            <input v-model="input.address" class="box__form-input" :style="'border: ' + input.border" name="address" type="text" placeholder="Enter domain, website or IP here" autofocus>
                            <button class="box__form-button" :disabled='btn.isDisabled' type="submit" form="search_form" value="Submit">{{ btn.btnTxt }}</button>
                        </form>
                    </div>
                    <div class="box__footer">
                        <div v-if="globalErrors">
                            <span v-for="(error, key) in globalErrors" :key="key"></span>
                        </div>
                        <div class="box__progress" v-if="progressBar.value">
                            <progress-bar
                            :options="progressBar.options"
                            :value="progressBar.value"
                            />
                            <div class="box__progress-container">
                                <span class="box__progress-message">{{ progressBar.message }}</span>
                                <spinner></spinner>
                            </div>
                        </div>
                        <span v-if="input.warning" class="box__providers-text--white warning-text">{{ input.warning }}</span>
                        <div class="box__providers-container" v-if="results && !progressBar.value">
                            <div class="box__providers-wrapper">
                                <div class="box__providers">
                                    <img class="box__providers-icon" src="/assets/images/search-search-logo.png">
                                    <span class="box__providers-text--white text-padding">Google Safe Browsing:</span>
                                    <span :class="'text-padding box__providers-text--' + fontColor(googlePositives)">{{ googlePositives }}</span>
                                    <span class="text-padding box__providers-text--white">{{ thePluraliser(googlePositives) }} found.</span>
                                    <span class="text-padding box__providers-text--red report-link" v-if="google_results.errors" @click="loadErrorModal('go')">Errors</span>
                                    <span class="text-padding box__providers-text--white report-link" @click="loadReportModal('go')">View report</span>
                                </div>
                                <div class="box__providers">
                                    <img class="box__providers-icon" src="/assets/images/url-scan-logo.png">
                                    <span class="box__providers-text text-padding">UrlScan.io:</span>
                                    <span :class="'text-padding box__providers-text--' + fontColor(scanCombinedVerdict)"> {{ scanCombinedVerdict }}%</span>
                                    <span class="text-padding box__providers-text--white">score.</span>
                                    <span class="text-padding box__providers-text--red report-link" v-if="scanio_results.errors" @click="loadErrorModal('sc')">Errors</span>
                                    <span class="text-padding box__providers-text--white report-link" @click="loadReportModal('sc')">View report</span>
                                </div>
                                <div class="box__providers">
                                    <img class="box__providers-icon" src="/assets/images/virus-total-logo.png">
                                    <span class="box__providers-text--white text-padding">Virus Total:</span>
                                    <span :class="'text-padding box__providers-text--' + fontColor(virusTotalPositives)">{{ virusTotalPositives}}</span>
                                    <span class="text-padding box__providers-text--white">{{ thePluraliser(virusTotalPositives) }} found.</span>
                                    <span class="text-padding box__providers-text--red report-link" v-if="virus_total_results.errors" @click="loadErrorModal('vt')">Errors</span>
                                    <span class="text-padding box__providers-text--white report-link" @click="loadReportModal('vt')">View report</span>
                                </div>
                            </div>
                        </div>
                        <span class="box__footer-text">Created by <a href="https://www.leighlatham.xyz" target="_blank">Leigh Latham</a></span>
                    </div>
                </div>
            </div>
            <error-modal    v-if="modal.showError"
                            :show-error.sync="modal.showError"
                            :google-errors="google_results.errors"
                            :scan-errors="scanio_results.errors"
                            :virus-total-errors="virus_total_results.errors"
                            :load-errors="error">
            </error-modal>
            <report-modal   v-if="modal.showReport"
                            :show-report.sync="modal.showReport"
                            :google-results="google_results.result"
                            :scan-results="scanio_results.result"
                            :virus-total-results="virus_total_results.result"
                            :load-report="report">
            </report-modal>
        </div>
</template>

<script>
import axios from 'axios'
import Spinner from './assets/Spinner.vue'
import ErrorModal from './global/GlobalErrorComponent.vue'
import ReportModal from './global/GlobalReportComponent.vue'
import ProgressBar from 'vuejs-progress-bar'
Vue.use(ProgressBar)

    export default {
        components: {
            Spinner,
            ErrorModal,
            ReportModal
        },
        data() {
            return {
                modal: {
                    showError: false,
                    showReport: false,
                },
                results: false,
                google_results: {
                    errors: null,
                    result: null
                },
                scanio_results: {
                    errors: null,
                    result: null,
                    verdicts: {
                        community: null,
                        engines: null,
                        overall: null,
                        urlscan: null
                    },
                    errors: null
                },
                virus_total_results: {
                    errors: null,
                    result: null
                },
                progressBar: {
                    value: 0,
                    message: null,
                    options: {
                        text: {
                            color: '#FFFFFF',
                            shadowEnable: true,
                            shadowColor: '#000000',
                            fontSize: 14,
                            fontFamily: 'Helvetica',
                            dynamicPosition: false,
                            hideText: false
                        },
                        progress: {
                            color: '#2dbd2d',
                            backgroundColor: '#333333',
                            inverted: false
                        },
                        layout: {
                            height: 35,
                            width: 140,
                            verticalTextAlign: 61,
                            horizontalTextAlign: 43,
                            zeroOffset: 0,
                            strokeWidth: 30,
                            progressPadding: 0,
                            type: 'line'
                        }
                    }
                },
                btn: {
                    btnTxt: 'Scan',
                    isDisabled: false,
                },
                notValid: false,
                input: {
                    address: null,
                    border: 0,
                    warning: null
                },
                error: null,
                report: null
            }
        },
        methods: {
            async submitSearch() {
                var self = this
                var form = new FormData()

                self.resetValues()
                if (!self.isValidAddress(self.input.address)) { return self.notValid = true }

                self.btnControl('Scanning', true)
                self.updateProgressBar(1, "Starting URL scanner")
                form.append('address', self.input.address)

                self.updateProgressBar(10, "Referencing Google Safe-Search database")

                await self.searchGoogle(self, form)
                await self.searchScanio(self, form)
                await self.searchVirusTotal(self, form)

                self.updateProgressBar(100, "Scan complete")
                self.progressBar.value = false
                self.results = true
                self.input.border = '1px solid green'
                self.btnControl('Scan', false)

            },
            async searchGoogle(self, form) {
                self.updateProgressBar(15, "Sending request to Google API")

                await axios({
                    headers: { 'Content-Type': 'multipart/form-data' },
                    method: 'post',
                    url: '/check_google',
                    data: form
                })
                .then(function (response) {
                    self.updateProgressBar(20, "Successfully recieved response from Google API")
                    return self.google_results.result = response
                })
                .catch(function (response) {
                    return self.google_results.errors = response
                });
            },
            async searchScanio(self, form) {
                self.updateProgressBar(30, "Sending request to Scan IO API")

                await axios({
                    headers: { 'Content-Type': 'multipart/form-data' },
                    method: 'post',
                    url: '/check_scan',
                    data: form
                })
                .then(function (response) {
                    self.updateProgressBar(50, "Successfully recieved response from Scan IO API")

                    if (response.data.status === 400) {
                        return self.scanio_results.errors = response.data.description
                    }

                    return self.scanio_results.result = response
                })
                .catch(function (response) {
                    return self.scanio_results.errors = response
                });
            },
            async searchVirusTotal(self, form) {

                await axios({
                    headers: { 'Content-Type': 'multipart/form-data' },
                    method: 'post',
                    url: '/check_virus',
                    data: form
                })
                .then(function (response) {
                    self.updateProgressBar(80, "Successfully recieved response from Virus Total API")
                    return self.virus_total_results.result = response
                })
                .catch(function (response) {
                    return self.virus_total_results.errors = response
                });
            },
            fontColor(res) {

                if (res > 0) {
                    return 'red'
                }

                return 'green'
            },
            btnControl(txt, state) {
                this.btn.btnTxt = txt
                this.btn.isDisabled = state
            },
            thePluraliser(res) {
                if (res == 1) {
                    return 'issue'
                }

                return 'issues'
            },
            updateProgressBar(value, message) {
                this.progressBar.value = value
                this.progressBar.message = message
            },
            resetValues() {
                this.input.warning = null
                this.input.border = 0
                this.google_results.errors = null
                this.scanio_results.errors = null
                this.virus_total_results.errors = null
            },
            isValidAddress(string) {
                var pattern = new RegExp('^(https?:\\/\\/)?'+
                    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+
                    '((\\d{1,3}\\.){3}\\d{1,3}))'+
                    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+
                    '(\\?[;&a-z\\d%_.~+=-]*)?'+
                    '(\\#[-a-z\\d_]*)?$','i');
                return !!pattern.test(string);
            },
            loadErrorModal(str) {
                this.error = str
                this.modal.showError = true
            },
            loadReportModal(str) {
                this.report = str
                this.modal.showReport = true
            }
        },
        watch: {
            notValid(val) {
                if (val) {
                    let data = this.input

                    data.address = null
                    data.border = '1px solid red'
                    data.warning = 'Please enter a valid URL or IP address.'
                }
            },
            globalErrors(err) {
                let self = this

                if (err) {
                    err.forEach(error => {
                        switch(error.name) {
                            case 'google':
                                return self.google_results.errors = error.error
                            case 'scan':
                                return self.scanio_results.errors = error.error
                            case 'virus':
                                return self.virus_total_results.errors = error.error
                            default:
                                return null
                        }
                    })
                }
            }
        },
        computed: {
            googleData() {
                if (this.results) {
                    return this.google_results.result.data
                }

                return null;
            },
            scanioData() {
                if (this.results) {
                    return this.scanio_results.result
                }

                return null;
            },
            virusTotalData() {
                if (this.results) {
                    return this.virus_total_results.result
                }

                return null;
            },
            googlePositives() {
                if (Object.keys(this.googleData).length > 0) {
                    return this.googleData.matches.length
                }

                return 0
            },
            scanCombinedVerdict() {
                if (this.scanioData) {
                    if (this.scanioData.data = "error") {
                        return 0
                    }else {
                        var score = 0;

                        Object.keys(this.scanioData.data.verdicts).forEach(obj => {
                            if (obj.score) {
                                score++
                            }
                        });
                    }
                }

                return 0;
            },
            virusTotalPositives() {
                if (this.virusTotalData) {
                    return this.virusTotalData.data.positives
                }

                return 0
            },
            globalErrors() {
                let errorArr = new Array

                if (this.google_results.errors) {
                    errorArr.push({name: 'google', value: 'Google Safe Browsing', error: this.google_results.errors})
                }

                if (this.scanio_results.errors) {
                    errorArr.push({name: 'scan', value: 'Scan IO', error: this.scanio_results.errors})
                }

                if (this.virus_total_results.errors) {
                    errorArr.push({name: 'virus', value: 'Virus Total', error: this.virus_total_results.errors})
                }

                if (errorArr.length > 0) {
                    return errorArr
                }

                return null
            }
        }
    }
</script>
