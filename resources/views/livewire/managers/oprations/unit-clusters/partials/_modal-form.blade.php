{{-- Modal Create --}}
 <x-managers.ui.modal title="Form Tipe Kamar" :show="$showModal && $modalType === 'form'" class="max-w-md">
     <form wire:submit.prevent="save" class="space-y-4">
         <x-managers.form.label>Nama Cluster</x-managers.form.label>
         <x-managers.form.input wire:model.live="name" placeholder="Contoh: Gedung A.." />

         {{-- PIC (Staff) - REMOVED --}}
         {{-- <x-managers.form.label>Staff Penanggung Jawab</x-managers.form.label>
         <x-managers.form.select wire:model.live="staffId" :options="$staffOptions" label="Pilih Staff" /> --}}

         <x-managers.form.label>Alamat Cluster</x-managers.form.label>
         <x-managers.form.input wire:model.live="address" placeholder="Contoh: Jl. Raya No. 123" />

         <x-managers.form.label>Deskripsi Tipe</x-managers.form.label>
         <x-managers.form.textarea wire:model.live="description" rows="3" />

         <x-managers.form.label>Gambar Tipe</x-managers.form.label>
         @if ($image)
             <div class="inline-flex gap-2 border border-gray-300 rounded p-2 mb-2">
                 <x-managers.form.small>Preview</x-managers.form.small>
                 <img src="{{ $image instanceof \Illuminate\Http\UploadedFile ? $image->temporaryUrl() : asset('storage/' . $image) }}"
                     alt="Preview Gambar" class="w-16 h-16 object-cover rounded border" />
             </div>
         @endif

         <div class="mb-2">
             @if ($errors->has('image'))
                 <span class="text-red-500 text-sm">{{ $errors->first('image') }}</span>
             @else
                 <x-managers.form.small>Max 2MB. JPG, PNG, GIF</x-managers.form.small>
             @endif
         </div>

         <x-filepond::upload wire:model.live="image" />

         <div class="flex justify-end gap-2 mt-10">
             <x-managers.ui.button type="button" variant="secondary"
                 wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
             <x-managers.ui.button wire:click="save()" variant="primary">
                 Simpan
             </x-managers.ui.button>
         </div>
     </form>
 </x-managers.ui.modal>