<template>
    <div class="submissions-card">
        <h3>{{ t('forms_dashboard_recent_submissions') }}</h3>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ t('forms_name') }}</th>
                        <th>{{ t('forms_phone') }}</th>
                        <th>Email</th>
                        <th>{{ t('forms_form_title') }}</th>
                        <th>{{ t('forms_received_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in items"
                        :key="item.id"
                        class="clickable-row"
                        @click="goToSubmission(item.id)"
                    >
                        <td>{{ item.name || '-' }}</td>
                        <td>{{ item.phone || '-' }}</td>
                        <td>{{ item.email || '-' }}</td>
                        <td>{{ item.form_title || '-' }}</td>
                        <td>{{ formatDate(item.created_at) }}</td>
                        <td>
                            <div class="item-actions" @click.stop>
                                <span :title="t('forms_view')" @click="goToSubmission(item.id)">
                                    <Icon name="eye" :size="16" />
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="6">{{ t('forms_no_data') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import Icon from '@reno-cms/components/common/Icon';
import { getAdminPrefix } from '../../api/forms';
import { t } from '../../i18n';

export default {
    name: 'RecentSubmissionsBlock',
    components: {
        Icon,
    },
    props: {
        data: {
            type: Object,
            required: true,
            default: () => ({
                items: [],
            }),
        },
    },
    computed: {
        items() {
            return Array.isArray(this.data?.items) ? this.data.items : [];
        },
    },
    methods: {
        t(key, params = {}) {
            return t(key, params);
        },
        goToSubmission(submissionId) {
            this.$router.push(`/${getAdminPrefix()}/forms/submissions/${submissionId}`);
        },
        formatDate(value) {
            if (!value) {
                return '-';
            }

            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleString();
        },
    },
};
</script>

<style scoped>
.submissions-card h3 {
    margin: 0 0 0.75rem 0;
    font-size: 0.9rem;
}

.clickable-row {
    cursor: pointer;
}
</style>
