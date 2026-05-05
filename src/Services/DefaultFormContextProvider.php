<?php

namespace Reno\Forms\Services;

use Reno\Cms\Models\Resource;
use Reno\Forms\Interfaces\Services\FormSubmissionContextProviderInterface;

class DefaultFormContextProvider implements FormSubmissionContextProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        $request = request();
        $resource = null;
        $resourceId = null;
        if (app()->bound(Resource::class)) {
            $resource = app(Resource::class);
            if ($resource instanceof Resource) {
                $resourceId = $resource->getId();
            }
        }

        $contextId = $request->attributes->get('cms_context_id');
        if ((!is_int($contextId) && !is_numeric($contextId)) && app()->bound('cms.current_context_id')) {
            $contextId = app('cms.current_context_id');
        }

        if ((!is_int($contextId) && !is_numeric($contextId)) && $resource instanceof Resource) {
            $contextId = $resource->context_id;
        }

        if (!is_int($contextId) && !is_numeric($contextId)) {
            $contextId = null;
        }

        return [
            'context_id' => $contextId !== null ? (int) $contextId : null,
            'resource_id' => $resourceId,
            'referrer' => $request->headers->get('referer'),
            'url' => $request->fullUrl(),
            'utm' => [
                'source' => $request->input('utm_source'),
                'medium' => $request->input('utm_medium'),
                'campaign' => $request->input('utm_campaign'),
                'term' => $request->input('utm_term'),
                'content' => $request->input('utm_content'),
            ],
            'user' => $request->user() ? [
                'id' => $request->user()->getKey(),
                'email' => $request->user()->email ?? null,
                'name' => $request->user()->name ?? null,
            ] : null,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getFieldsMapping(): array
    {
        return [
            'resource_id' => __('forms::forms.context_resource_id'),
            'referrer' => __('forms::forms.context_referrer'),
            'url' => __('forms::forms.context_submission_url'),
            'utm.source' => 'UTM Source',
            'utm.medium' => 'UTM Medium',
            'utm.campaign' => 'UTM Campaign',
            'utm.term' => 'UTM Term',
            'utm.content' => 'UTM Content',
            'user.id' => __('forms::forms.context_user_id'),
            'user.email' => __('forms::forms.context_user_email'),
            'user.name' => __('forms::forms.context_user_name'),
        ];
    }
}
