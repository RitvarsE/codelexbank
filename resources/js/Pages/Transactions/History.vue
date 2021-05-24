<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transaction history
            </h2>
        </template>
        <div v-for="transaction in transactions" :key="transaction">
            <div class="mt-4 flex items-center justify-center px-4">
                <div class="max-w-4xl  bg-white w-full rounded-lg shadow-xl">
                    <div class="p-3 border-b">
                        <p class="text-sm text-gray-500">
                            Receipt #{{transaction.id}}
                        </p>
                    </div>
                    <div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Sender`s name
                            </p>
                            <p>
                                {{transaction.sender.name}}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Senders`s account
                            </p>
                            <p>
                                {{transaction.sender_account_number}}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Receiver`s name
                            </p>
                            <p>
                                {{transaction.receiver.name}}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Receiver`s account number
                            </p>
                            <p>
                                {{transaction.receiver_account_number}}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Amount
                            </p>
                            <p>
                               {{formatCurrency(transaction.amount, transaction.currency)}}
                            </p>
                        </div>
                        <div v-if="transaction.tax" class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Tax
                            </p>
                            <p>
                                {{ formatCurrency(transaction.tax, transaction.currency) }}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Purpose of payment
                            </p>
                            <p>
                                {{transaction.purpose}}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Purpose of payment
                            </p>
                            <p>
                                {{transaction.created_at}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
    components: {
        AppLayout,
    },
    beforeMount() {
        this.getTransactions()
    },
    methods: {
        getTransactions() {
            axios.get('/api/history')
                .then(res => {
                    this.transactions = res.data
                })
                .catch(error => {
                    console.log(error.message)
                })
        },
        formatCurrency(money, currency){
           return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(money/100)
        },
    },
    data() {
        return {
            transactions: []
        }
    }
}
</script>

<style scoped>

</style>
