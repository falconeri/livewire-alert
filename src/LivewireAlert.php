<?php

namespace Jantinnerezo\LivewireAlert;

use Illuminate\Support\Arr;
use Jantinnerezo\LivewireAlert\Exceptions\AlertException;

trait LivewireAlert
{
    public function confirm(string $title, $options = [])
    {
        $options = array_merge(
            config('livewire-alert.confirm'),
            $options,
        );

        $type = Arr::get($options, 'icon');
        
        $this->alert($type,$title, $options);
    }

    public function alert(string $type = 'success', string $message = '', array $options = [])
    {
        $this->dispatchOrFlashAlert([
            'type' => $type,
            'message' => $message,
            'options' => $options
        ]);
    }

    public function flash(string $type = 'success', string $message = '', array $options = [], $redirect = '/')
    {
        $this->dispatchOrFlashAlert([
            'type' => $type,
            'message' => $message,
            'options' => $options,
            'flash' => true
        ]);

        return redirect()->to($redirect);
    }

    protected function dispatchOrFlashAlert(array $configuration)
    {
        $type = Arr::get($configuration, 'type');
        $message = Arr::get($configuration, 'message');
        $options = Arr::only(
            Arr::get($configuration, 'options'),
            $this->configurationKeys()
        );
        $isFlash = Arr::has($configuration, 'flash') && Arr::get($configuration, 'flash') === true;

        $options = array_merge(
            config('livewire-alert.alert') ?? [],
            config('livewire-alert.' . $type) ?? [],
            $options
        );

        if (! in_array($type,$this->livewireAlertIcons())) {
            throw new AlertException(
                "Invalid '{$type}' alert icon."
            );
        }

        $payload = [
            'type' => $type,
            'message' => $message,
            'events' => Arr::only($options, $this->livewireAlertEvents()),
            'options' => Arr::except($options, $this->livewireAlertEvents())
        ];

        if (! $isFlash) {
            $this->dispatchBrowserEvent('alert', $payload);

            return;
        }
        
        session()->flash('livewire-alert', $payload);
    }

    protected function livewireAlertIcons(): array
    {
        return [
            'success',
            'info',
            'warning',
            'error',
            'question'
        ];
    }

    protected function livewireAlertEvents(): array
    {
        return [
            'onConfirmed', 
            'onDismissed', 
            'onDenied',
            'onProgressFinished'
        ];
    }

    protected function configurationKeys(): array
    {
        return [
            'title',
            'titleText',
            'html',
            'text',
            'icon',
            'iconColor',
            'iconHtml',
            'showClass',
            'hideClass',
            'footer',
            'backdrop',
            'toast',
            'target',
            'input',
            'width',
            'padding',
            'background',
            'position',
            'grow',
            'customClass',
            'timer',
            'timerProgressBar',
            'heightAuto',
            'allowOutsideClick',
            'allowEscapeKey',
            'allowEnterKey',
            'stopKeydownPropagation',
            'keydownListenerCapture',
            'showConfirmButton',
            'showDenyButton',
            'showCancelButton',
            'confirmButtonText',
            'denyButtonText',
            'cancelButtonText',
            'confirmButtonText',
            'confirmButtonColor',
            'denyButtonColor',
            'cancelButtonColor',
            'confirmButtonAriaLabel',
            'denyButtonAriaLabel',
            'cancelButtonAriaLabel',
            'buttonsStyling',
            'reverseButtons',
            'focusConfirm',
            'returnFocus',
            'focusDeny',
            'focusCancel',
            'showCloseButton',
            'closeButtonHtml',
            'closeButtonAriaLabel',
            'loaderHtml',
            'showLoaderOnConfirm',
            'showLoaderOnDeny',
            'scrollbarPadding',
            'imageUrl',
            'imageWidth',
            'imageHeight',
            'imageAlt',
            'inputLabel',
            'inputPlaceholder',
            'inputValue',
            'inputOptions',
            'inputAutoTrim',
            'inputAttributes',
            'events'
        ];
    }
}