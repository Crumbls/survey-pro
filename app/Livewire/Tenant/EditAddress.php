<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Masterix21\Addressable\Models\Address;

class EditAddress extends Component implements HasForms
{
    use InteractsWithForms;

    public string $modelId;
    public string $modelType;

    protected Model $parent;

    public array $data;

    public function mount(string $modelId, string $modelType)
    {
        $this->modelId = $modelId;
        $this->modelType = $modelType;

        // Initialize the form first
        $this->form = $this->form($this->makeForm());

        $model = $this->getParent();

        // If the model has an address, load it
        if ($address = $model->addresses()->first()) {
            $this->data = $address->toArray();
            $this->data['type'] =
                array_keys(array_filter([
                    'is_primary' => $this->data['is_primary'],
                    'is_billing' => $this->data['is_billing'],
                    'is_shipping' => $this->data['is_shipping'],
                ]));

            $this->form->fill($this->data);
        } else {
            // Initialize with default values
            $this->form->fill([
                'country' => 'US',
                'type' => 'main',
            ]);
        }
return;
        // Debug information
        logger()->info('Form initialized', [
            'modelId' => $this->modelId,
            'modelType' => $this->modelType,
            'hasAddress' => isset($address),
            'formSchema' => $this->form->getSchema(),
        ]);
    }

    protected function getParent(): Model
    {
        if (isset($this->parent)) {
            return $this->parent;
        }

        $class = $this->modelType;
        $record = $class::find($this->modelId);
        abort_if(!$record, 404);
        $this->parent = $record;
        return $this->parent;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('street_address1')
                            ->label('Street Address')
                            ->required(),

                        TextInput::make('street_address2')
                            ->label('Apartment, suite, etc.')
                            ->nullable(),

                        TextInput::make('city')
                            ->required(),

                        Select::make('state')
                            ->options($this->getStates())
                            ->searchable()
                            ->required(),

                        TextInput::make('zip')
                            ->label('ZIP/Postal Code')
                            ->required(),

                        Select::make('country')
                            ->options($this->getCountries())
                            ->searchable()
                            ->required()
                            ->default('US'),

                        Select::make('type')
                            ->options([
                                'is_primary' => 'Primary',
                                'is_billing' => 'Billing',
                                'is_shipping' => 'Shipping',
                            ])
                            ->required()
                            ->multiple()
                            ->default('is_primary'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $model = $this->getParent();

        foreach([
            'is_primary',
            'is_billing',
            'is_shipping'
                ] as $type) {
            $data[$type] = in_array($type, $data['type']);
        }

        unset($data['type']);

        if ($address = $model->addresses()->first()) {
            $address->update($data);
        } else {
            $model->addresses()->create($data);
        }

        $this->dispatch('address-updated');

        session()->flash('success', 'Address updated successfully.');

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    protected function getStates(): Collection
    {
        return collect([
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            // Add remaining states...
        ]);
    }

    protected function getCountries(): array
    {
        return [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
        ];
    }

    public function render()
    {
        return view('livewire.tenant.edit-address', [
            'record' => $this->getParent()
        ]);
    }
}
