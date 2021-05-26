<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>
        <div class="py-3">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 border-b">
                        <p class="text-sm text-gray-500">
                            Accounts
                        </p>
                    </div>
                    <div v-for="account in bankAccounts" :key="account">
                        <div class="mt-4 flex items-center justify-center px-4 mb-3">
                            <div class="max-w-2xl  bg-white w-full rounded-lg shadow-xl">
                                <div>
                                    <div
                                        class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                                        <p class="text-gray-600">
                                            Number
                                        </p>
                                        <p>
                                            {{ account.number }}
                                        </p>
                                    </div>
                                    <div
                                        class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                                        <p class="text-gray-600">
                                            Currency
                                        </p>
                                        <p>
                                            {{ account.currency }}
                                        </p>
                                    </div>
                                    <div
                                        class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                                        <p class="text-gray-600">
                                            Amount
                                        </p>
                                        <p>
                                            {{ formatCurrency(account.amount, account.currency) }}
                                        </p>
                                    </div>
                                    <div
                                        class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                                        <p class="text-gray-600">
                                            Type
                                        </p>
                                        <p>
                                            {{ account.type === 0 ? 'Debit' : 'Saving' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'

export default {
    data() {
        return {
            bankAccounts: [],
        }
    },
    components: {
        AppLayout,
        Welcome,
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
        formatCurrency(money, currency) {
            return new Intl.NumberFormat('en-US', {style: 'currency', currency: currency}).format(money / 100)
        },
    },

}
</script>
