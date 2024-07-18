<template>
    <v-app  class="background scrollable-section" >
        <v-main>
            <router-view/>
            <HelloWorld />
            <v-btn color="primary">Hello Vuetify</v-btn>
        </v-main>
    </v-app>
</template>

<script>
import HelloWorld from './components/HelloWorld.vue';

export default {
    components: {
        HelloWorld,
    },
    mounted: function() {
        let elHtml = document.getElementsByTagName('html')[0]
        elHtml.style.overflowY = 'hidden'
    },
    destroyed: function() {
        let elHtml = document.getElementsByTagName('html')[0]
        elHtml.style.overflowY = null
    },
};
</script>

<style scoped>
.status-bar {
    background-color: transparent !important;
}
.scrollable-section {
    overflow-y: auto;
    max-height: 100vh;
}
</style>
