import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import { createExtensionConfig } from '../reno-cms/tools/vite/createExtensionConfig.mjs';

const packageDirectory = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig(
    createExtensionConfig({
        packageDirectory,
        base: '/vendor/reno/forms/build/',
        entryDefinitions: [
            {
                type: 'file',
                name: 'components/forms/FormSubmissionsListPage',
                relativePath: 'components/forms/FormSubmissionsListPage.vue',
            },
            {
                type: 'file',
                name: 'components/forms/FormSubmissionShowPage',
                relativePath: 'components/forms/FormSubmissionShowPage.vue',
            },
            {
                type: 'file',
                name: 'components/forms/ConsentAcceptancesPage',
                relativePath: 'components/forms/ConsentAcceptancesPage.vue',
            },
        ],
        externalizeCmsRuntime: true,
    }),
);
