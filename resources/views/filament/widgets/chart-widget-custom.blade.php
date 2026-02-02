@php
    use Filament\Support\Facades\FilamentView;

    $color = $this->getColor();
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $filters = $this->getFilters();
@endphp

<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section :description="$description" :heading="$heading">
        @if ($filters)
            <x-slot name="headerEnd">
                <x-filament::input.wrapper
                    inline-prefix
                    wire:target="filter"
                    class="w-max sm:-my-2"
                >
                    <x-filament::input.select
                        inline-prefix
                        wire:model.live="filter"
                    >
                        @foreach ($filters as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-slot>
        @endif

        <div
            @if ($pollingInterval = $this->getPollingInterval())
                wire:poll.{{ $pollingInterval }}="updateChartData"
            @endif
        >
            <div
                @if (FilamentView::hasSpaMode())
                    x-load="visible"
                @else
                    x-load
                @endif
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                x-data="chart({
                            cachedData: @js($this->getCachedData()),
                            options: {
                                ...(@js($this->getOptions())),
                                animation: {
                                    onComplete: function(context) {
                                        const chart = context.chart;
                                        const ctx = chart.ctx;
                                        ctx.save();
                                        
                                        // Default alignment (Pie/Doughnut)
                                        let align = 'center';
                                        let offsetX = 0;

                                        // Check for Horizontal Bar
                                        const isHorizontalBar = chart.config.type === 'bar' && 
                                            (chart.options.indexAxis === 'y' || chart.config.options?.indexAxis === 'y');

                                        // Check for Vertical Bar
                                        const isVerticalBar = chart.config.type === 'bar' && !isHorizontalBar;

                                        let offsetY = 0;

                                        if (isHorizontalBar) {
                                            align = 'right';
                                            offsetX = -10; // Padding from right edge
                                        } else if (isVerticalBar) {
                                            align = 'center';
                                            offsetY = 15; // Move down inside the bar
                                            // Handle case where bar is too short? 
                                            // For now assume bars are tall enough or text overflows naturally.
                                        }

                                        ctx.textAlign = align;
                                        ctx.textBaseline = 'middle';
                                        ctx.font = 'bold 12px sans-serif';
                                        ctx.fillStyle = '#ffffff';

                                        // Loop through datasets
                                        chart.data.datasets.forEach((dataset, i) => {
                                            const meta = chart.getDatasetMeta(i);
                                            if (meta.hidden) return;

                                            // Loop through data elements
                                            meta.data.forEach((element, index) => {
                                                const dataVal = dataset.data[index];
                                                if (!dataVal || dataVal === 0) return;

                                                // Calculate Percentage
                                                const total = dataset.data.reduce((acc, curr) => acc + curr, 0);
                                                const percentage = ((dataVal / total) * 100).toFixed(1) + '%';
                                                
                                                // Defaults
                                                let {x, y} = element.tooltipPosition();
                                                let textAlign = 'center';
                                                let textColor = '#ffffff';
                                                let useShadow = true;
                                                let offsetX = 0;
                                                let offsetY = 0;

                                                // Get Label Text
                                                const category = chart.data.labels[index];
                                                let labelText = category; 
                                                let valueText = percentage;
                                                let isBar = false; // Flag to check if it's a bar chart

                                                // --- SMART PLACEMENT LOGIC ---
                                                if (isHorizontalBar) {
                                                    isBar = true;
                                                    labelText = ''; // Hide Category Name (Already on Y Axis)
                                                    valueText = dataVal + ' (' + percentage + ')';
                                                    
                                                    // Calculate Bar Width
                                                    const barBase = element.base;
                                                    const barTip = element.x;
                                                    const barWidth = Math.abs(barTip - barBase);

                                                    // Measure Text Width (Only Value Text now)
                                                    ctx.font = 'bold 12px sans-serif'; 
                                                    const textWidth = ctx.measureText(valueText).width;
                                                    
                                                    if (barWidth > (textWidth + 25)) { // Less padding needed for single line
                                                        // Fits INSIDE
                                                        textAlign = 'right';
                                                        offsetX = -10;
                                                        textColor = '#ffffff';
                                                        useShadow = true;
                                                    } else {
                                                        // Fits OUTSIDE
                                                        textAlign = 'left';
                                                        offsetX = 10;
                                                        textColor = '#374151'; 
                                                        useShadow = false; 
                                                    }
                                                } else if (isVerticalBar) {
                                                     isBar = true;
                                                     labelText = ''; // Hide Category Name (Already on X Axis)
                                                     valueText = dataVal + ' (' + percentage + ')';
                                                     textAlign = 'center';
                                                     offsetY = 15;
                                                }

                                                // Apply Offsets
                                                x += offsetX;
                                                y += offsetY;

                                                // Apply Styles
                                                ctx.textAlign = textAlign;
                                                ctx.textBaseline = 'middle';
                                                ctx.fillStyle = textColor;

                                                if (useShadow) {
                                                    ctx.shadowColor = 'rgba(0, 0, 0, 0.7)';
                                                    ctx.shadowBlur = 3; 
                                                    ctx.shadowOffsetX = 1;
                                                    ctx.shadowOffsetY = 1;
                                                } else {
                                                    ctx.shadowColor = 'transparent';
                                                    ctx.shadowBlur = 0;
                                                    ctx.shadowOffsetX = 0;
                                                    ctx.shadowOffsetY = 0;
                                                }

                                                if (isBar) {
                                                    // SINGLE LINE FOR BARS
                                                    ctx.font = 'bold 12px sans-serif';
                                                    ctx.fillText(valueText, x, y);
                                                } else {
                                                    // DOUBLE LINE FOR PIE/DOUGHNUT
                                                    // Draw Label (Top Line)
                                                    ctx.font = 'bold 11px sans-serif'; 
                                                    ctx.fillText(labelText, x, y - 9);
                                                    
                                                    // Draw Value/Percentage (Bottom Line)
                                                    ctx.font = 'bold 12px sans-serif';
                                                    ctx.fillText(valueText, x, y + 9);
                                                }
                                            });
                                        });
                                        ctx.restore();
                                    }
                                }
                            },
                            type: @js($this->getType()),
                        })"
                x-init="
                    // FORCE REGISTER PLUGIN IF AVAILABLE IN WINDOW
                    if (window.ChartDataLabels && window.Chart) {
                        try {
                            window.Chart.register(window.ChartDataLabels);
                        } catch(e) { console.error('Gagal register plugin', e); }
                    }
                "
                @class([
                    match ($color) {
                        'gray' => null,
                        default => 'fi-color-custom',
                    },
                    is_string($color) ? "fi-color-{$color}" : null,
                ])
            >
                <canvas
                    x-ref="canvas"
                    x-ref="canvas"
                    @if ($maxHeight = $this->getMaxHeight())
                        style="height: {{ $maxHeight }};"
                    @endif
                ></canvas>

                {{-- Hidden background elements for checks --}}
                <span x-ref="backgroundColorElement" @class([ match ($color) { 'gray' => 'text-gray-100 dark:text-gray-800', default => 'text-custom-50 dark:text-custom-400/10', }, ]) @style([ \Filament\Support\get_color_css_variables($color, shades: [50, 400], alias: 'widgets::chart-widget.background') => $color !== 'gray', ])></span>
                <span x-ref="borderColorElement" @class([ match ($color) { 'gray' => 'text-gray-400', default => 'text-custom-500 dark:text-custom-400', }, ]) @style([ \Filament\Support\get_color_css_variables($color, shades: [400, 500], alias: 'widgets::chart-widget.border') => $color !== 'gray', ])></span>
                <span x-ref="gridColorElement" class="text-gray-200 dark:text-gray-800"></span>
                <span x-ref="textColorElement" class="text-gray-500 dark:text-gray-400"></span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
