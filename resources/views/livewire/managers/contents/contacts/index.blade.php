<div>
    <x-managers.ui.card title="Kontak">
        <form wire:submit.prevent="save">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <flux:input
                        label="Nomor Telepon"
                        type="text"
                        wire:model="phoneNumber"
                        placeholder="+62 21 1234 5678"
                        required
                        :error="$errors->first('phoneNumber')"
                    />
                </div>

                <div class="col-md-6 mb-3">
                    <flux:input
                        label="Email"
                        type="email"
                        wire:model="email"
                        placeholder="bpu@unj.ac.id"
                        required
                        :error="$errors->first('email')"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <flux:input
                        label="Jam Operasional"
                        type="text"
                        wire:model="operationalHours"
                        placeholder="Senin - Jumat, 08:00 - 16:00"
                        required
                        :error="$errors->first('operationalHours')"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <flux:input
                        label="Alamat"
                        type="textarea"
                        rows="3"
                        wire:model.live="address"
                        placeholder="Jl. Pemuda No. 10, Rawamangun, Jakarta Timur, Dki Jakarta 13220"
                        required
                        maxlength="200"
                        :error="$errors->first('address')"
                    >
                        <x-slot name="after">
                            <div class="text-end text-muted mt-1">
                                <small>{{ strlen($address) }}/200</small>
                            </div>
                        </x-slot>
                    </flux:input>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <flux:button variant="primary" type="submit" class="mt-3">Update</flux:button>
                </div>
            </div>
        </form>
    </x-managers.ui.card>
</div>