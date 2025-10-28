# market-channel.com

<?/* 감춤처리 <!--
-->*/?>

작업 9/10

10/27
사업자 확인 api 키 받아서해야함
https://www.data.go.kr/data/15081808/openapi.do




<script>

var data = {
    "b_no": ["6741600175"] // 사업자번호 "xxxxxxx" 로 조회 시,
   };

$.ajax({
  url: "https://api.odcloud.kr/api/nts-businessman/v1/status?serviceKey=3f8ac7f3cd980dbd77f13e137a2af9e6c295e1b702c9c88b3873d306700b76ab",  // serviceKey 값을 xxxxxx에 입력
  type: "POST",
  data: JSON.stringify(data), // json 을 string으로 변환하여 전송
  dataType: "JSON",
  contentType: "application/json",
  accept: "application/json",
  success: function(result) {
      console.log(result.data[0]['b_stt_cd']); // 정상처리시 사업자 상태 01 출력
  },
  error: function(result) {
      console.log(result.responseText); //responseText의 에러메세지 확인
  }
});
</script>
// 개인 61e139f30e4b68ea414aa7a1df396f9f3acf6ac882a4b71456e3e2cd5b32f63f
// 기업 3f8ac7f3cd980dbd77f13e137a2af9e6c295e1b702c9c88b3873d306700b76ab
