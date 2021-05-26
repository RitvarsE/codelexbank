<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buying stocks
            </h2>
        </template>

        <div v-if="form.stockPrice > 0 || $page.props.errors.stockAmount" class="popup">
            <div class="center">

                <div v-if="$page.props.errors.stockAmount"
                     class="text-red-500"> Incorrect amount
                </div>

                You can buy {{ roundToTwo(calculateAmount) }} '{{ form.stockName }}' stock.<br>
                Stock price: {{ formatCurrency(form.stockPrice, form.bankAccount.currency) }}
                <form @submit.prevent="buyStock">
                    <input class="input"
                           v-model="form.stockAmount"
                           name="buyStock"
                           type="number"
                           placeholder="Stock amount"
                           step=".01"
                           :max="roundToTwo(calculateAmount)"
                           required>

                    <button class="btn btn-success ml-1"
                            :disabled="form.stockAmount < 0"> Buy
                    </button>
                </form>

                <div>
                    <button type="button" class="btn btn-primary mt-2"
                            @click="backButton"> Back
                    </button>
                </div>
            </div>
        </div>
        <div v-else class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div style="margin-bottom: -24px"
                     v-if="form.bankAccount.number === null ">Please select account
                </div>
                <form method="post" @submit.prevent="findStock">
                    <select class="input"
                            v-model="form.bankAccount"
                            name="bankAccount"
                            id="bankAccount"
                            required
                    >
                        <option v-for="account in bankAccounts"
                                :key="account"
                                :value="{number: account.number, amount: account.amount, currency: account.currency}">
                            {{ account.number }} {{ formatCurrency(account.amount, account.currency) }}
                        </option>
                    </select>
                    <div v-if="form.bankAccount.amount !== null">
                        <div style="margin-bottom: -35px"><label for="purpose">Find your desirable stock</label></div>
                        <input class="input"
                               v-model="form.stockName"
                               id="purpose"
                               type="text"
                               placeholder="Stock name"
                               required>
                        <div>
                            <button style="margin-top: 10px"
                                    class="btn btn-primary"
                                    :disabled="form.stockName.length < 1">
                                Find
                            </button>
                        </div>
                    </div>
                </form>
                <div v-if="form.stockPrice === 0 || invalidStock"
                     style="margin-bottom: -24px"
                     class="text-red-500">
                    Invalid stock name!
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

export default {
    components: {
        AppLayout
    },
    data() {
        return {
            bankAccounts: [],
            form: this.$inertia.form({
                bankAccount: {
                    number: null,
                    amount: null,
                    currency: null,
                },
                stockName: '',
                stockAmount: null,
                stockPrice: null
            }),
            invalidStock: false,
        }
    },
    methods: {
        getBankAccounts() {
            axios.get('/api/getUserAccounts/').then((response) => {
                this.bankAccounts = response.data.filter(account => account.type === 1)
            })
        },
        formatCurrency(money, currency) {
            return new Intl.NumberFormat('en-US', {style: 'currency', currency: currency}).format(money / 100)
        },
        findStock() {
            axios.get('/api/getQuote/', {
                params: {
                    currency: this.form.bankAccount.currency,
                    stock: this.form.stockName
                }
            })
                .then(res => {
                    if (res.data === 0) {
                        this.invalidStock = true
                        this.form.stockPrice = res.data
                    } else {
                        this.invalidStock = false
                        this.form.stockPrice = res.data
                    }
                })
                .catch(() => this.invalidStock = true)
        },
        roundToTwo(num) {
            return +(Math.round(num + "e+2") + "e-2");
        },
        buyStock() {
            this.form.post('/api/buyStock/')
        },
        backButton() {
            this.form.reset('stockPrice','stockAmount')
            this.$page.props.errors.stockAmount = null
        }
    },
    beforeMount() {
        this.getBankAccounts()
    },
    computed: {
        calculateAmount() {
            return this.form.bankAccount.amount / this.form.stockPrice
        }
    }
}
</script>

<style scoped>
.input {
    margin-top: 30px;
    width: 300px;
}

.popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    display: flex;
    justify-content: center;
    width: 33.33%;
    height: 300px;
}

.center {
    align-self: center;
    padding: 2rem;
}
</style>
