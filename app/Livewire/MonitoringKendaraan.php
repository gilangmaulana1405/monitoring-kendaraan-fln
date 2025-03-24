<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\File;

class MonitoringKendaraan extends Component
{

    public $kendaraan;
    protected $listeners = ['refreshKendaraan' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData(){
        $this->kendaraan =  Kendaraan::all()->map(function ($k) {
            $imgPath = public_path('img/');
            $extensions = ['jpeg', 'jpg', 'png', 'webp'];
            $image = 'img/default.png';

            foreach ($extensions as $ext) {
                if (File::exists($imgPath . $k->nopol . '.' . $ext)) {
                    $image = 'img/' . $k->nopol . '.' . $ext;
                    break;
                }
            }

            $k->image_path = $image;
            return $k;
        });
    }

    public function update()
    {
        $this->validate([
            'status' => 'required|string',
            'nama_pemakai' => 'nullable|string',
            'departemen' => 'nullable|string',
            'tujuan' => 'nullable|string',
        ]);

        $kendaraan = Kendaraan::findOrFail($this->id);
        $kendaraan->status = $this->status;
        $kendaraan->nama_pemakai = $this->nama_pemakai;
        $kendaraan->departemen = $this->departemen;

        // Simpan tujuan hanya jika status "Pergi"
        if ($this->status == "Pergi") {
            $kendaraan->nama_pemakai = $this->nama_pemakai;
            $kendaraan->departemen = $this->departemen;
            $kendaraan->tujuan = $this->tujuan;
        } else {
            $kendaraan->nama_pemakai = null;
            $kendaraan->departemen = null;
            $kendaraan->tujuan = null;
        }

        $kendaraan->save();

        $this->loadData();
        $this->dispatch('refreshKendaraan');

        // reset form setelah update
        $this->reset(['id', 'nama_mobil', 'nopol', 'status', 'tujuan', 'nama_pemakai', 'departemen']);
        
        session()->flash('message', "Status kendaraan {$kendaraan->nama_mobil} {$kendaraan->nopol} berhasil diperbarui!");
    }

    public function render()
    {
        return view('livewire.monitoring-kendaraan');
    }
}
