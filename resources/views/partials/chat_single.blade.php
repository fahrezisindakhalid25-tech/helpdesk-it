<div class="flex justify-end mb-4 animate-fade-in-up">
    <!-- USER MESSAGE (RIGHT) -->
    <div class="flex items-end max-w-[85%] sm:max-w-[75%] flex-row-reverse gap-2">
        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 text-xs font-bold flex-shrink-0 order-1">
            {{ substr($ticket->nama_lengkap, 0, 1) }}
        </div>
        <div class="order-2">
            <div class="flex items-center justify-end gap-2 mb-1 mr-1">
                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $ticket->nama_lengkap }}</span>
            </div>
             <div class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 p-4 rounded-2xl rounded-br-none shadow-sm relative group transition-all hover:shadow-md">
                <div class="prose prose-sm dark:prose-invert max-w-none break-words leading-relaxed trix-content">
                     {!! $comment->content !!}
                </div>
                
                @php
                    $rawAttachments = is_string($comment->attachments) ? json_decode($comment->attachments, true) : $comment->attachments;
                    $attachments = collect($rawAttachments)->flatten()->filter();
                @endphp
                @if($attachments->count() > 0)
                    <div class="mt-3 grid grid-cols-2 gap-2 dir-rtl">
                        @foreach($attachments as $img)
                             <div class="relative group/img overflow-hidden rounded-lg">
                                 <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/10 transition-colors pointer-events-none"></div>
                                 <img src="{{ asset('storage/' . $img) }}" onclick="openModal(this.src)" class="w-full h-24 object-cover cursor-zoom-in transition-transform group-hover/img:scale-105">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="flex items-center justify-end gap-1 mt-1 mr-1">
                <span class="text-[10px] text-gray-400">{{ $comment->created_at->format('H:i') }}</span>
                <span class="text-[10px] text-blue-500 font-medium">Delivered</span>
            </div>
        </div>
    </div>
</div>
