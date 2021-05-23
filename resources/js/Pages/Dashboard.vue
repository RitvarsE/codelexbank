<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div v-for="account in bankAccounts" :key="account">
                        {{ account.number }} - {{ account.amount }}
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
    props: ['user'],
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
        }
    },

}
</script>
