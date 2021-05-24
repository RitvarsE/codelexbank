<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sending money
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div>
                    <div style="margin-bottom: -24px" v-if="form.senderAccount.amount === null ">Please select account
                    </div>
                    <form method="post" @submit.prevent="sendMoney">
                        <select class="input"
                                v-model="form.senderAccount"
                                name="senderAccount"
                                id="senderAccount"
                                required
                                @change="excludingSenderAccount">

                            <option disabled :value="null">Select bank account</option>
                            <option v-for="account in bankAccounts"
                                    :key="account"
                                    :value="{number: account.number, amount: account.amount, type: account.type}">
                                {{ this.$props.user.name }}
                                {{ account.number }} - {{ account.amount }}
                            </option>
                        </select>
                        <div v-if="form.senderAccount.amount !== null">
                            <div>
                                <input class="input"
                                       v-model="form.receiver"
                                       name="receiver"
                                       type="text"
                                       placeholder="Receiver"
                                       required>
                            </div>
                            <div v-if="form.errors.receiver"
                                 style="margin-bottom: -24px"
                                 class="text-red-500">{{ form.errors.receiver }}
                            </div>
                            <div v-if="form.senderAccount.type === 1">
                                <select class="input"
                                        v-model="form.receiverAccount"
                                        name="receiverAccount"
                                        id="receiverAccount"
                                        required>

                                    <option disabled :value="null">Select bank account</option>
                                    <option v-for="account in excludeSenderAccount"
                                            :key="account"
                                            :value="account.number">
                                        <b>{{ account.type === 0 ? 'Debit' : 'Saving' }}</b>:
                                        {{ account.number }} - {{ account.amount }}
                                    </option>
                                </select>
                            </div>
                            <div v-else><input v-model="form.receiverAccount"
                                               name="receiverAccount"
                                               class="input"
                                               type="text"
                                               placeholder="Receiver account number"
                                               minlength="20"
                                               maxlength="20"
                                               @input="getAccountByNumber"
                                               required
                            >
                            </div>
                            <div v-if="invalidAccount && form.receiverAccount.length === 20"
                                 style="margin-bottom: -24px">
                                Invalid account number
                            </div>
                            <div><input
                                v-model="form.sendingAmount"
                                name="sendingAmount"
                                id="sendingAmount"
                                class="input"
                                type="number"
                                placeholder="Amount"
                                :disabled="amountToLow"
                                required
                            >
                                <select name="currency"
                                        id="currency"
                                        v-model="form.currency"
                                        required>
                                    <option value="EUR">EUR</option>
                                    <option v-for="currency in currencies" :key="currency" :value="currency.name">
                                        {{ currency.name }}
                                    </option>
                                </select>
                                <div v-if="amountToLow"
                                     style="margin-bottom: -24px"
                                     class="text-red-500">You don`t have money in this account
                                </div>
                                <div v-if="form.errors.sendingAmount"
                                     style="margin-bottom: -24px"
                                     class="text-red-500">{{ form.errors.sendingAmount }}
                                </div>
                            </div>
                            <div>
                                <input class="input"
                                       v-model="form.purpose"
                                       name="purpose"
                                       type="text"
                                       placeholder="Purpose of payment"
                                       required>
                            </div>
                            <button style="margin-top: 10px"
                                    class="btn btn-primary"
                                    :disabled="invalidAccount">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
                <div v-if="message">{{ message }}</div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {useForm} from '@inertiajs/inertia-vue3'

export default {
    props: ['message', 'user'],
    components: {
        AppLayout
    },
    data() {
        return {
            bankAccounts: [],
            invalidAccount: false,
            excludeSenderAccount: [],
            currencies: [],
        }
    },
    setup() {
        const form = useForm({
            senderAccount: {
                amount: null,
                number: null,
                type: null,
            },
            sendingAmount: null,
            receiverAccount: null,
            purpose: null,
            receiver: null,
            currency: null,

        })
        return {form}
    },
    beforeMount() {
        this.getData()
        this.getCurrencies()
    },
    methods: {
        getData() {
            axios.get('/api/getUserAccounts/').then((response) => {
                this.bankAccounts = response.data
            })
        },
        sendMoney() {
            this.form.post('/transaction/', {})
        },
        getCurrencies() {
            axios.get('/api/getCurrencies/')
                .then(res => {
                    this.currencies = res.data
                    console.log(res.data)
                })
                .catch(error => console.log(error.message));
        }
    },
    computed: {
        amountToLow() {
            if (this.form.senderAccount.amount <= 0) {
                this.form.sendingAmount = null
            }
            return this.form.senderAccount.amount <= 0
        },
        getAccountByNumber() {
            if (this.form.receiverAccount.length === 13) {
                this.invalidAccount = false
                axios.get('/api/getAccountByNumber', {params: {account: this.form.receiverAccount}})
                    .then(res => {
                        this.invalidAccount = res.data.length === 0
                    })
            }
        },
        excludingSenderAccount() {
            this.excludeSenderAccount = _.cloneDeep(this.bankAccounts)
            this.excludeSenderAccount.splice(this.excludeSenderAccount.indexOf(this.form.senderAccount.number), 1)
        }
    }
}
</script>
<style>
.input {
    margin-top: 30px;
    width: 300px;
}
</style>
