import { createVuetify } from 'vuetify';
import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css'
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { VDateInput } from 'vuetify/labs/VDateInput';

export default createVuetify({
    components: {
        ...components,
        VDateInput
    },
    directives,
    icons: {
        defaultSet: 'mdi',
    },
});
