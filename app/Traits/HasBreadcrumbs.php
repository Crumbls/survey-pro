<?php

namespace App\Traits;

trait HasBreadcrumbs
{
    /**
     * The breadcrumb collection
     *
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * Add a breadcrumb to the trail
     *
     * @param string $label
     * @param string|null $url
     * @return self
     */
    protected function addBreadcrumb(string $label, ?string $url = null): self
    {
        $this->breadcrumbs[] = [
            'label' => $label,
            'url' => $url,
        ];

        return $this;
    }

    /**
     * Get all breadcrumbs
     *
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Set the default resource breadcrumbs based on the current action
     *
     * @param string $resourceName
     * @param string $modelName
     * @param mixed|null $model
     * @return self
     */
    protected function setResourceBreadcrumbs(string $resourceName, string $modelName, $model = null): self
    {
        // Add home breadcrumb
        $this->addBreadcrumb('Home', route('home'));

        // Add resource index
        $this->addBreadcrumb(
            str($resourceName)->plural()->title(),
            route($resourceName . '.index')
        );

        // Get the current action
        $action = $this->getCurrentAction();

        // Add action-specific breadcrumbs
        switch ($action) {
            case 'create':
                $this->addBreadcrumb('Create New');
                break;

            case 'show':
                if ($model) {
                    $this->addBreadcrumb($this->getModelTitle($model));
                }
                break;

            case 'edit':
                if ($model) {
                    $this->addBreadcrumb(
                        $this->getModelTitle($model),
                        route($resourceName . '.show', $model)
                    );
                    $this->addBreadcrumb('Edit');
                }
                break;
        }

        return $this;
    }

    /**
     * Set Livewire component breadcrumbs
     *
     * @param string $componentName
     * @param array $additionalCrumbs
     * @return self
     */
    protected function setLivewireBreadcrumbs(string $componentName, array $additionalCrumbs = []): self
    {
        // Add home breadcrumb
        $this->addBreadcrumb('Home', route('home'));

        // Add component base breadcrumb
        $this->addBreadcrumb(
            str($componentName)->headline(),
            route(str($componentName)->kebab()->plural()->toString())
        );

        // Add any additional breadcrumbs
        foreach ($additionalCrumbs as $crumb) {
            $this->addBreadcrumb(
                $crumb['label'],
                $crumb['url'] ?? null
            );
        }

        return $this;
    }

    /**
     * Get the current controller action
     *
     * @return string|null
     */
    protected function getCurrentAction(): ?string
    {
        if (method_exists($this, 'getActionMethod')) {
            return $this->getActionMethod();
        }

        $action = request()->route()->getActionMethod();
        return str($action)->before('Action')->toString();
    }

    /**
     * Get a readable title from a model
     *
     * @param mixed $model
     * @return string
     */
    protected function getModelTitle($model): string
    {
        if (method_exists($model, 'getBreadcrumbTitle')) {
            return $model->getBreadcrumbTitle();
        }

        return $model->title ?? $model->name ?? "#{$model->id}";
    }
}
