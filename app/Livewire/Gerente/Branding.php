<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ConfiguracionEmpresa;
use Illuminate\Support\Facades\Storage;

class Branding extends Component
{
    use WithFileUploads;

    public $nombre_comercial = '';
    public $logo;
    public $currentLogoPath = null;
    
    public $configId = null;

    protected function rules()
    {
        return [
            'nombre_comercial' => 'required|string|max:150',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // JPG/PNG and <2MB
        ];
    }

    public function mount()
    {
        $config = ConfiguracionEmpresa::first();
        if ($config) {
            $this->configId = $config->id;
            $this->nombre_comercial = $config->nombre_comercial;
            $this->currentLogoPath = $config->logo_path;
        }
    }

    public function save()
    {
        $this->validate();

        $logoPath = $this->currentLogoPath;
        if ($this->logo) {
            // Delete old logo
            if ($this->currentLogoPath) {
                Storage::disk('public')->delete($this->currentLogoPath);
            }
            $logoPath = $this->logo->store('company', 'public');
        }

        $config = ConfiguracionEmpresa::find($this->configId);
        if ($config) {
            $config->update([
                'nombre_comercial' => $this->nombre_comercial,
                'logo_path' => $logoPath,
            ]);
        } else {
            ConfiguracionEmpresa::create([
                'nombre_comercial' => $this->nombre_comercial,
                'logo_path' => $logoPath,
            ]);
        }

        $this->currentLogoPath = $logoPath;
        $this->logo = null;
        
        session()->flash('success', 'Configuración de Branding corporativo actualizada con éxito.');
    }

    public function render()
    {
        return view('livewire.gerente.branding')
            ->layout('layouts.app');
    }
}
