<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sorry, something went wrong!
            </h2>
        </template>
        {{$page.props.code}}
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
    components: {
        AppLayout
    }
}
</script>

<style scoped>

</style>
