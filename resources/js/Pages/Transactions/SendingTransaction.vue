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
                                required>

                            <option disabled :value="null">Select bank account</option>
                            <option v-for="account in bankAccounts"
                                    :key="account"
                                    :value="{number: account.number, amount: account.amount}">
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
                            <div><input v-model="form.receiverAccount"
                                        name="receiverAccount"
                                        class="input"
                                        type="text"
                                        placeholder="Receiver account number"
                                        minlength="13"
                                        maxlength="13"
                                        @input="getAccountByNumber"
                                        required
                                        oninvalid="this.setCustomValidity('Username cannot be blank')">
                            </div>
                            <div v-if="invalidAccount && form.receiverAccount.length === 13"
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
                                        id="currency">
                                    <option value="EUR">EUR</option>
                                    <option value="USD">USD</option>
                                </select>
                                <div v-if="checkAmount"
                                     style="margin-bottom: -24px">
                                    You don`t have enough money
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
                                    :disabled="checkAmount || invalidAccount">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
                <div v-if="form.errors">{{ form.errors }}</div>
                <div v-if="message">{{ message }}</div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {useForm} from '@inertiajs/inertia-vue3'

export default {
    props: ['message'],
    components: {
        AppLayout
    },
    data() {
        return {
            bankAccounts: [],
            invalidAccount: false
        }
    },
    setup() {
        const form = useForm({
            senderAccount: {
                amount: null,
                number: null
            },
            sendingAmount: null,
            receiverAccount: null,
            purpose: null,
            receiver: null,

        })
        return {form}
    },
    beforeMount() {
        this.getData()
    },
    methods: {
        getData() {
            axios.get('/api/getUserAccounts/').then((response) => {
                this.bankAccounts = response.data
            })
        },
        sendMoney() {
            this.form.post('/transaction/', {})
        }
    },
    computed: {
        checkAmount() {
            return this.form.senderAccount.amount < this.form.sendingAmount
        },
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
