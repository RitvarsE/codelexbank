<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sending money
            </h2>
        </template>
        <div v-if="$page.props.code || finishing || $page.props.errors.code" class="popup">
            <div class="center">
                <div v-if="$page.props.errors.code" class="text-red-500"> Incorrect code</div>
                Check your email for verification code
                <form @submit.prevent="verifyCode">
                    <input class="input"
                           v-model="verify.code"
                           name="verificationCode"
                           type="text"
                           placeholder="verificationCode"
                           required>
                    <div>
                        <button class="btn btn-primary mt-2"
                                :disabled="verify.code.length < 20"> Verify
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div v-else class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div>
                    <div style="margin-bottom: -24px"
                         v-if="form.senderAccount.amount === null ">Please select account
                    </div>
                    <form method="post" @submit.prevent="validate">
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
                                {{ account.number }} {{ formatCurrency(account.amount, account.currency) }}
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
                                        {{ account.number }} - {{ formatCurrency(account.amount, account.currency) }}
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
                                :max="form.senderAccount.amount/100"
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
                                    :disabled="amountToLow || $page.props.code">
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
    props: ['user', 'code'],
    components: {
        AppLayout
    },
    data() {
        return {
            finishing: false,
            bankAccounts: [],
            excludeSenderAccount: [],
            form: this.$inertia.form({
                senderAccount: {
                    amount: null,
                    number: null,
                    type: null,
                },
                sendingAmount: null,
                receiverAccount: null,
                purpose: null,
                receiver: null,
            }),
            verify: this.$inertia.form({
                code: '',
                userIndex: this.$props.user.id
            })
        }
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
        validate() {
            this.form.post('/api/validation/')
        },
        verifyCode() {
            this.verify.post('/api/validateVerification/', {
                onSuccess: () => {
                    this.form.post('/api/sendMoney/')
                },
            })
        },
        formatCurrency(money, currency) {
            return new Intl.NumberFormat('en-US', {style: 'currency', currency: currency}).format(money / 100)
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
            /// šis jāsataisa, kaut kas nenostrādā.
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
