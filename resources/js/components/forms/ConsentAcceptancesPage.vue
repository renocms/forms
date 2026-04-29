<template>
    <div class="admin-page">
        <div class="page-header no-bottom">
            <h1>{{ translate('forms_form_consents') }}</h1>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ translate('forms_title') }}</th>
                        <th>{{ translate('forms_name') }}</th>
                        <th>{{ translate('forms_phone') }}</th>
                        <th>Email</th>
                        <th>{{ translate('forms_user_name') }}</th>
                        <th>{{ translate('forms_form_title') }}</th>
                        <th>{{ translate('forms_received_at') }}</th>
                        <th>{{ translate('forms_revoked_at') }}</th>
                        <th>{{ translate('forms_revoked_by') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="acceptance in acceptances" :key="acceptance.id">
                        <td>{{ acceptance.title || '-' }}</td>
                        <td>{{ acceptance.name || '-' }}</td>
                        <td>{{ acceptance.phone || '-' }}</td>
                        <td>{{ acceptance.email || '-' }}</td>
                        <td>
                            <router-link
                                v-if="acceptance.user?.id"
                                :to="`/${adminPrefix}/users/${acceptance.user.id}`"
                            >
                                {{ acceptance.user.name || `#${acceptance.user.id}` }}
                            </router-link>
                            <span v-else>-</span>
                        </td>
                        <td>{{ acceptance.form_title || '-' }}</td>
                        <td>{{ formatDate(acceptance.created_at) }}</td>
                        <td>{{ formatDate(acceptance.deleted_at) }}</td>
                        <td>
                            <router-link
                                v-if="acceptance.deleted_by_user?.id"
                                :to="`/${adminPrefix}/users/${acceptance.deleted_by_user.id}`"
                            >
                                {{ acceptance.deleted_by_user.name || `#${acceptance.deleted_by_user.id}` }}
                            </router-link>
                            <span v-else>-</span>
                        </td>
                        <td>
                            <div class="item-actions" @click.stop>
                                <span
                                    v-if="!acceptance.deleted_at"
                                    :title="translate('forms_revoke_consent')"
                                    @click="handleDelete(acceptance.id)"
                                >
                                    <Icon name="trash-2" :size="16" />
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="acceptances.length === 0">
                        <td colspan="10">{{ translate('forms_no_data') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <PaginationControls
            :meta="meta"
            :loading="loading"
            @change-page="changePage"
        />
    </div>
</template>

<script>
import Icon from '@reno-cms/components/common/Icon';
import PaginationControls from '@reno-cms/components/common/PaginationControls';
import { deleteConsentAcceptance, getAdminPrefix, getConsentAcceptances } from '../../api/forms';
import { t as formT } from '../../i18n';

export default {
    name: 'ConsentAcceptancesPage',
    components: {
        Icon,
        PaginationControls,
    },
    data() {
        return {
            acceptances: [],
            adminPrefix: getAdminPrefix(),
            loading: false,
            perPage: 20,
            meta: {
                current_page: 1,
                last_page: 1,
                per_page: 20,
                total: 0,
                from: null,
                to: null,
            },
        };
    },
    async mounted() {
        await this.loadAcceptances();
    },
    methods: {
        translate(key, params = {}) {
            return formT(key, params);
        },
        async loadAcceptances(page = 1) {
            this.loading = true;

            try {
                const response = await getConsentAcceptances({
                    page,
                    per_page: this.perPage,
                });
                this.acceptances = response.data || [];
                this.meta = {
                    ...this.meta,
                    ...(response.meta || {}),
                };
            } finally {
                this.loading = false;
            }
        },
        async changePage(page) {
            if (page < 1 || page > this.meta.last_page) {
                return;
            }

            await this.loadAcceptances(page);
        },
        async handleDelete(id) {
            if (!confirm(this.translate('forms_confirm_revoke_consent'))) {
                return;
            }

            await deleteConsentAcceptance(id);
            await this.loadAcceptances(this.meta.current_page);
        },
        formatDate(value) {
            if (!value) {
                return '-';
            }

            return new Date(value).toLocaleString();
        },
    },
};
</script>
