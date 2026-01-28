@forelse($ticket->comments as $comment)
    <div class="flex {{ $comment->user_id ? 'justify-start' : 'justify-end' }} mb-4 animate-fade-in-up">
        
        @if($comment->user_id)
            <!-- ADMIN MESSAGE (LEFT) -->
            <div class="flex items-end max-w-[85%] sm:max-w-[75%] gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold shadow-md flex-shrink-0 order-1">
                    A
                </div>
                <div class="order-2">
                    <div class="flex items-center gap-2 mb-1 ml-1">
                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300">Admin Support</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-4 rounded-2xl rounded-bl-sm shadow-sm border border-gray-100 dark:border-gray-700 relative group transition-all hover:shadow-md">
                        <div class="prose prose-sm dark:prose-invert max-w-none break-words leading-relaxed trix-content">
                            {!! $comment->content !!}
                        </div>
                        
                        @php
                            $rawAttachments = is_string($comment->attachments) ? json_decode($comment->attachments, true) : $comment->attachments;
                            $attachments = collect($rawAttachments)->flatten()->filter();
                        @endphp
                        @if($attachments->count() > 0)
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                @foreach($attachments as $img)
                                    <div class="relative group/img overflow-hidden rounded-lg">
                                        <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/10 transition-colors pointer-events-none"></div>
                                        <img src="{{ asset('storage/' . $img) }}" onclick="openModal(this.src)" class="w-full h-24 object-cover cursor-zoom-in transition-transform group-hover/img:scale-105">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="text-[10px] text-gray-400 mt-1 ml-1">{{ $comment->created_at->format('H:i') }}</div>
                </div>
            </div>

        @else
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
                        @if($loop->last && $ticket->status != 'Closed')
                             <span class="text-[10px] text-blue-500 font-medium">Delivered</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
@empty
    <div class="flex flex-col items-center justify-center py-10 opacity-60">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-3">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        </div>
        <p class="text-gray-500 text-sm">Belum ada percakapan.</p>
        <p class="text-gray-400 text-xs mt-1">Jadilah yang pertama menulis pesan.</p>
    </div>
@endforelse
