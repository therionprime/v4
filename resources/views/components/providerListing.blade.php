

@php 
$provider = $model->provider;
$type = strtolower(class_basename($model));
@endphp
<div class="flex items-center justify-between mb-2">
    @component('components.tinyProviderIcon', ["provider" => $provider, "size" => 30])@endcomponent

    <div class="flex-grow ml-2">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="">
                <div>{{ $provider->name }}</div>
                @if($model->working)
                <div class="text-green-light font-bold text-sm">
                    <span class="mr-1">Working</span>
                    <i class="fas fa-check-circle"></i>
                </div>
                @else
                <div class="text-red-light font-bold text-sm">
                    <span class="mr-1">Revoked</span>
                    <i class="fas fa-times-octagon"></i>
                </div>
                @endif
            </div>
        </div>
        <div class="flex items-center">
            @component('components.ipaOrItmsButton', [
                "model" => $model, 
                "image" => false, 
                "class" => ""])
            @endcomponent
            @admin
                @component('components.button', [
                    "href"=> "/nova/resources/$type/$model->id/edit", 
                    "bg" => "red", 
                    "color" => "white", 
                    "class" => "ml-2"])
                     EDIT 
                @endcomponent
            @endadmin
        </div>
        
    </div>
    @if($showLine ?? true)
        <hr class="border-0 border-b mb-0 {{ theme('border-gray-200') }}">
    @endif
    </div>
</div>