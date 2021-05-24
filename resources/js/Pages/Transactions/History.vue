<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Open new account
            </h2>
            <div v-for="transaction in transactions" :key="transaction">
                {{ transaction }}
            </div>
        </template>
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
        }
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
