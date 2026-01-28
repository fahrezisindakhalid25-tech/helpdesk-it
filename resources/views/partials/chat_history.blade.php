@forelse($ticket->comments as $comment)
    <div class="flex {{ $comment->user_id ? 'justify-start' : 'justify-end' }}">
        
        @if($comment->user_id)
            <div class="flex gap-3 max-w-[80%]">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    A
                </div>
                <div>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 shadow-sm">
                        <p class="text-sm font-bold text-blue-600 mb-1">Admin Support</p>
                        <div class="text-gray-800 dark:text-gray-200 text-sm whitespace-pre-wrap trix-content">{!! $comment->content !!}</div>
                        @php
                            $rawAttachments = is_string($comment->attachments) ? json_decode($comment->attachments, true) : $comment->attachments;
                            $attachments = collect($rawAttachments)->flatten()->filter();
                        @endphp
                        @if($attachments->count() > 0)
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                @foreach($attachments as $img)
                                    <img src="{{ asset('storage/' . $img) }}" onclick="openModal(this.src)" class="rounded-lg border border-gray-200 shadow-sm cursor-pointer hover:opacity-90 max-w-[150px]">
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <span class="text-xs text-gray-400 ml-1">{{ $comment->created_at->format('H:i') }}</span>
                </div>
            </div>

        @else
            <div class="flex gap-3 max-w-[80%] flex-row-reverse">
                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    U
                </div>
                <div>
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 shadow-sm">
                        <p class="text-sm font-bold text-green-700 dark:text-green-400 mb-1">Anda</p>
                        <div class="text-gray-800 dark:text-gray-200 text-sm whitespace-pre-wrap trix-content">{!! $comment->content !!}</div>
                        @php
                            $rawAttachments = is_string($comment->attachments) ? json_decode($comment->attachments, true) : $comment->attachments;
                            $attachments = collect($rawAttachments)->flatten()->filter();
                        @endphp
                        @if($attachments->count() > 0)
                            <div class="mt-2 grid grid-cols-2 gap-2 justify-items-end">
                                @foreach($attachments as $img)
                                    <img src="{{ asset('storage/' . $img) }}" onclick="openModal(this.src)" class="rounded-lg border border-gray-200 shadow-sm cursor-pointer hover:opacity-90 max-w-[150px]">
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <span class="text-xs text-gray-400 mr-1 text-right block">{{ $comment->created_at->format('H:i') }}</span>
                </div>
            </div>
        @endif

    </div>
@empty
    <p class="text-center text-gray-400 text-sm italic">Belum ada percakapan.</p>
@endforelse
