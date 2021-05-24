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
                    <div style="margin-bottom: -24px"
                         v-if="form.senderAccount.amount === null ">Please select account
                    </div>
                    <form method="post" @submit.prevent="sendMoney">
                        <select class="input"
                                v-model="form.senderAccount"
                                name="senderAccount"
                                id="senderAccount"
                                required
                                @change="excludingSenderAccount">
                            <option v-for="account in bankAccounts"
                                    :key="account"
                                    :value="{number: account.number, amount: account.amount, type: account.type}">
                                {{ account.type === 0 ? 'Debit' : 'Saving' }}:
                                {{ account.number }}  {{formatCurrency(account.amount, account.currency)}}
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
                                 class="text-red-500">Receiver name is not associated to bank account
                            </div>
                            <div v-if="form.senderAccount.type === 1">
                                <select class="input"
                                        v-model="form.receiverAccount"
                                        name="receiverAccount"
                                        id="receiverAccount"
                                        required>

                                    <option v-for="account in excludeSenderAccount"
                                            :key="account"
                                            :value="account.number">
                                        {{ account.type === 0 ? 'Debit' : 'Saving' }}:
                                        {{ account.number }} - {{formatCurrency(account.amount, account.currency)}}
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
                                               required>
                            </div>
                            <div v-if="form.errors.receiverAccount"
                                 style="margin-bottom: -24px" class="text-red-500">
                                Invalid account, please check again!
                            </div>
                            <div><input
                                v-model="form.sendingAmount"
                                name="sendingAmount"
                                id="sendingAmount"
                                class="input"
                                type="number"
                                placeholder="Amount"
                                :disabled="amountToLow"
                                step=".01"
                                required>
                                <div v-if="form.errors.sendingAmount"
                                     style="margin-bottom: -24px"
                                     class="text-red-500"> Your don`t have that much money
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
                                    :disabled="amountToLow">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
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
            excludeSenderAccount: [],
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
        },
        formatCurrency(money, currency){
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(money/100)
        },
    },
    computed: {
        amountToLow() {
            if (this.form.senderAccount.amount <= 0) {
                this.form.sendingAmount = null
            }
            return this.form.senderAccount.amount <= 0
        },
        excludingSenderAccount() {
            this.excludeSenderAccount = _.cloneDeep(this.bankAccounts)
            console.log(this.excludeSenderAccount)
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
