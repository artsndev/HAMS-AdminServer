<template>
    <v-app  class="background scrollable-section" >
        <v-main>
            <router-view/>
        </v-main>
    </v-app>
</template>

<script>

export default {
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
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap');
* {
    font-family: 'Poppins', sans-serif;
    background-color: #ECEFF1;
}
.status-bar {
    background-color: transparent !important;
}
.scrollable-section {
    overflow-y: auto;
    max-height: 100vh;
}
</style>
