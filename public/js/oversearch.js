({
    name: "oversearch event",

    getName: function () {
        return this.name;
    },

    init: function () {
        var user = $("#username_tag").html();
        var host = $(location).attr('host');
        var url = "http://"+host+"/main/research/user/"+user;
        var title = "오버서치 검색 : "+user;

        $('#favorites').on("click", function(e) {
            if (window.sidebar) { // Mozilla Firefox Bookmark
                window.sidebar.addPanel(title,url,'');
                return false;
            } else if(document.all) { // IE Favorite
                e.preventDefault();
                window.external.AddFavorite(url,title);
            }else { // webkit - safari/chrome
                e.preventDefault();
                alert('Press ' + (navigator.userAgent.toLowerCase().indexOf('mac') != - 1 ? 'Command/Cmd' : 'CTRL') + ' + D 를 클릭하세요.');
            }
        });

        $('#renewal').on("click", function(e) {
            var user = $("#username_tag").html();
            var host = $(location).attr('host');
            var to_url = "http://"+host+"/main/research/user/"+user;
            alert("전적을 갱신합니다!");
            $(location).attr('href', to_url);
        });

        $('.none_a').on("click", function(e) {
            var name = $(this).html();
            var return_name = "";
            var return_bool = true;
            switch (name) {
                //신규 챔프가 생겼을시 챔프에 따른 case문만 추가하면 확장됨.
                case "리퍼":
                    return_name = "Reaper";
                    return_url = "reaper";
                break;

                case "트레이서":
                    return_name = "Tracer";
                    return_url = "tracer";
                break;

                case "한조":
                    return_name = "Hanzo";
                    return_url = "hanzo";
                break;

                case "토르비욘":
                    return_name = "Torbjoern";
                    return_url = "torbjorn";
                break;

                case "라인하르트":
                    return_name = "Reinhardt";
                    return_url = "reinhardt";
                break;

                case "파라":
                    return_name = "Pharah";
                    return_url = "pharah";
                break;

                case "윈스턴":
                    return_name = "Winston";
                    return_url = "winston";
                break;

                case "위도우메이커":
                    return_name = "Widowmaker";
                    return_url = "widowmaker";
                break;

                case "바스티온":
                    return_name = "Bastion";
                    return_url = "bastion";
                break;

                case "시메트라":
                    return_name = "Symmetra";
                    return_url = "symmetra";
                break;

                case "젠야타":
                    return_name = "Zenyatta";
                    return_url = "zenyatta";
                break;

                case "겐지":
                    return_name = "Genji";
                    return_url = "genji";
                break;

                case "로드호그":
                    return_name = "Roadhog";
                    return_url = "roadhog";
                break;

                case "맥크리":
                    return_name = "McCree";
                    return_url = "mccree";
                break;

                case "정크랫":
                    return_name = "Junkrat";
                    return_url = "junkrat";
                break;

                case "자리야":
                    return_name = "Zarya";
                    return_url = "zarya";
                break;

                case "솔저: 76":
                    return_name = "Soldier76";
                    return_url = "soldier-76";
                break;

                case "루시우":
                    return_name = "Lucio";
                    return_url = "lucio";
                break;

                case "D.Va":
                    return_name = "D.Va";
                    return_url = "dva";
                break;

                case "메이":
                    return_name = "Mei";
                    return_url = "mei";
                break;

                case "아나":
                    return_name = "Ana";
                    return_url = "ana";
                break;

                case "메르시":
                    return_name = "Mercy";
                    return_url = "mercy";
                break;

                case "솜브라":
                    return_name = "Sombra";
                    return_url = "sombra";
                break;

                default :
                    return_name = name;
                    return_bool = false;
                    return_url = false;
            }
            var href = $(location).attr('href').split("#");
            var current = href[0];
            var to_url = current+"#"+return_name;

            $(location).attr('href',to_url);
        });
    }
}).init();