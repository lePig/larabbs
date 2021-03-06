@if (count($replies))

<ul class="list-group">
    @foreach ($replies as $reply)
        <li class="list-group-item">
            <a href="{{ $reply->topic->link(['#reply' . $reply->id]) }}">
                {{ $reply->topic->title }}
            </a>

            <div class="reply-content" style="margin: 6px 0;">
                {!! $reply->content !!}
            </div>

            <div class="meta">
                <span class="glyphicon glyphicon-time" aria-hidden="true"></span> 回复于 {{ $reply->created_at->diffForHumans() }}
            </div>
        </li>
    @endforeach
</ul>

@else
   <div class="empty-block">暂无数据 ~_~ </div>
@endif

{{-- 分页 --}}
{!! $replies->appends(Request::except('page'))->render() !!}
{{-- 我们在url中使用tab来对`话题`和`回复`进行区分。所以这里使用appends()可以对url中的参数进行继承 --}}


{{-- 分页的另一种写法如下，更加简洁https://laravel-china.org/docs/laravel/5.5/pagination#ed54e0 --}}
{{-- {!! $replies->appends(['tab' => 'replies'])->links() !!}  --}}

