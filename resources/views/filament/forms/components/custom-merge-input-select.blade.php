<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">  
    <x-filament::input.wrapper x-data="{
        state: $wire.$entangle('{{ $getStatePath() }}'),
        unit: 'm',
        factor: 1,
        originalValue: 0,
        updateValue() {
            if (this.unit === 'm') {
                this.state = this.originalValue;
            } else if (this.unit === 'mm') {
                this.state = this.originalValue * 1000;
            } else if (this.unit === 'cm') {
                this.state = this.originalValue * 100;
            } else if (this.unit === 'km') {
                this.state = this.originalValue / 1000;
            } else if (this.unit === 'm²') {
                this.state = this.originalValue ** 2;
            } else if (this.unit === 'cm²') {
                this.state = (this.originalValue * 100) ** 2;
            } else if (this.unit === 'mm²') {
                this.state = (this.originalValue * 1000) ** 2;
            } else if (this.unit === 'km²') {
                this.state = (this.originalValue / 1000) ** 2;
            }
        },
        onInputChange() {
            this.originalValue = this.unit === 'm' ? this.state : this.state / (this.unit === 'mm' ? 1000 : (this.unit === 'cm' ? 100 : 0.001));
        }
    }" class="flex">

        <x-filament::input x-model.number="state" x-on:input="onInputChange()"
            class="form-input block w-full rounded-none" placeholder="Enter surface area" />

        <x-slot:suffix>
            <x-filament::input.select {{-- style='border: none !important; background: transparent;--tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color)--tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(0px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);' --}} x-model="unit" x-on:change="updateValue()">
                @if ($exponent)
                    <option value="m²">m²</option>
                    <option value="cm²">cm²</option>
                    <option value="mm²">mm²</option>
                    <option value="km²">km²</option>
                @else
                    <option value="m">m</option>
                    <option value="cm">cm</option>
                    <option value="mm">mm</option>
                    <option value="km">km</option>
                @endif
            </x-filament::input.select>
        </x-slot:suffix>
    </x-filament::input.wrapper>
</x-dynamic-component>
