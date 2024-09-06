<?php

namespace App\Services\Classes;

use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload as BaseSpatieMediaLibraryFileUpload;

class SpatieMediaLibraryFileUpload extends BaseSpatieMediaLibraryFileUpload
{
    public function circle(bool | Closure $condition = false): static
    {

        $this->isAvatar = $condition;

        $this->image();
        $this->imageResizeMode('cover');
        $this->imageResizeUpscale(false);
        $this->imageCropAspectRatio('1:1');
        $this->imageResizeTargetHeight('500');
        $this->imageResizeTargetWidth('500');
        $this->loadingIndicatorPosition('center bottom');
        $this->panelLayout('compact circle');
        $this->removeUploadedFileButtonPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'left bottom' : 'center bottom');
        $this->uploadButtonPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'right bottom' : 'center bottom');
        $this->uploadProgressIndicatorPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'right bottom' : 'center bottom');

        return $this;
    }
}
