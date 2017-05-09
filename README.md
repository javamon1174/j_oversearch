
# JFramework 기반 오버워치 전적 검색

제이프레임워크를 적용한 샘플용 프로젝트.

### 개발환경
|서버| 운영체제|프로젝트 설명|개발기간|개발인원|작업환경|개발환경|
| ------------- |:-------------:| -----:|-----:|-----:|-----:|-----:|
|AWS|Ubuntu 16.04|제이프레임워크 기반 오버워치 검색|17.5.4~5|단독|MAC OS|PHP 7.0.*  APACHE2  MariaDB 10.*|

github link : [github](https://github.com/javamon1174/j_oversearch)
sample link : [demo](http://javamon.be/j_oversearch)
<h4>초기 화면</h4>
<p>같은 기능의 타 사이트와 같이, 심플한 검색전 뷰를 구성하였습니다.</p>
<div align="center">
    <img src="https://github.com/javamon1174/OverSearch/blob/master/%20screenshot/init.png?raw=true" />
</div>

<h4>검색 화면 - Main</h4>
<p>검색 뷰로 들어오면 블리자드 웹의 데이터를 파싱하여 데이터베이스에 저장 후, 뷰에 바인딩하여 보여줍니다.</p>
<p>기존 데이터가 있을 시 데이터베이스에서 데이터를 가져와 뷰에 바인딩하여 보여줍니다.</p>
<p>전적갱신 버튼을 통해 기존 데이터를 데이터베이스에서 삭제하고 새로 파싱합니다.</p>
<p>영웅 픽순위의 영웅 이름를 클릭하게 되면 #id값에 따라 디테일 뷰의 위치를 이동하도록 구현하였습니다.</p>
<div align="center">
    <img src="https://github.com/javamon1174/OverSearch/blob/master/%20screenshot/search.png?raw=true" />
</div>

<h4>검색 화면 - Detail</h4>
<p>데이터 수에 따라 동적으로 표현하는 레이아웃을 적용하였습니다.</p>
<div align="center">
    <img src="https://github.com/javamon1174/OverSearch/blob/master/%20screenshot/detail.png?raw=true" />
</div>