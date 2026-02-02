$(function() {
    $("#btn_more_item").on("click", function() {
        var $this = $(this);
        var url   = $this.data("url");
        var page  = $this.data("page");
        var $msg  = $("#item_load_msg");

        if($msg.is(":visible"))
            return false;

        if($this.hasClass("no_more_item")) {
            alert("등록된 상품이 더 이상없습니다.");
            return false;
        }

        $msg.css("display", "block");

        $.ajax({
            type: "POST",
            data: { page: page },
            url: url,
            cache: false,
            async: true,
            dataType: "json",
            success: function(data) {
                if(data.error != "") {
                    alert(data.error);
                    return false;
                }

                // var $items = $(data.item).find("li");
                var $items = data.item;
                // $items 는 HTML 문자열 또는 DOM 요소 배열로 전달될 수 있습니다.
                // 각각의 경우에 맞게 _item2 클래스의 개수를 계산합니다.

                var _item2_count = 0;

                if (typeof $items === "string") {
                    // HTML 문자열인 경우
                    // 임시 DOM 컨테이너에 넣고 세기
                    var $temp = $("<div>").html($items);
                    _item2_count = $temp.find("._item2").length;
                } else if ($.isArray($items) || ($items instanceof jQuery && $items.length > 0)) {
                    // DOM 요소 배열 또는 jQuery 객체인 경우
                    // 각 요소에서 _item2를 세고, 없으면 self가 _item2인지 확인
                    $($items).each(function(){
                        if ($(this).hasClass("_item2")) {
                            _item2_count++;
                        } else {
                            _item2_count += $(this).find("._item2").length;
                        }
                    });
                }

                // console.log로 결과를 알려줌 (원한다면 알림창 등으로 변경 가능)
                // console.log("_item2 count:", _item2_count);
                // var cnt = $items.length;

                if(_item2_count < 1) {
                    alert("등록된 상품이 더 이상없습니다.");
                    $msg.css("display", "none");
                    $this.addClass("no_more_item");
                    return false;
                }

                $("#sct_wrap").append($items);
                $this.data("page", data.page);
                $msg.css("display", "none");
            }
        });
    });
});