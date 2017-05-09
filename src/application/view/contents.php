<body>
  <!-- search form -->
  <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="navbar-form pull-right" name="myForm" role="search" onsubmit="return false;">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="닉네임#TAG" name="user" style="background-color:#5D5D5D;color:black" required>
            <div class="input-group-btn">
                <button class="btn btn-default" type="submit" onclick="validateForm();"><i class="glyphicon glyphicon-search"></i></button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
  </div>
  <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <!-- summary -->
      <div class="row">
          <div class="table-responsive" id="background_form" style="border:none;">
            <table class="table" border="0" cellspacing="0" cellpadding="0">
              <tbody id="borderless">
                <tr>
                  <td rowspan="3" style="width:120px;margin-left:2%;"><img class="img-responsive" src="<?=$summary[0]['icon']?>" id="icon"></img></td>
                  <td><span id="username_tag"><?=$summary[0]['user_name']?></span>(LV. <span id="user_level"><?=$summary[0]['level']?></span>) <img id="level_img" src="<?=$summary[0]['level_img']?>"/></td>
                </tr>
                <tr>
                  <td>
                    경쟁전 평점 : <span id="com_grade"><?=$summary[0]['com_grade']?></span><br>
                    최근 업데이트 : <span id="recent_date"><?=$time[0]['update_date']?></span>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="btn-group">
                      <button type="button" class="btn btn-warning btn-sm" id="renewal">전적갱신</button>
                      <button type="button" class="btn btn-danger btn-sm" id="favorites">즐겨찾기</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
           </div>
      </div>
      <!-- hero -->
      <div class="row">
        <div class="col-md-12" style="background-color:#5D5D5D; color: #8c8c8c"><br />
            <h3 style="text-align:center">당신의 계급은 "<span id="grade"><?=$summary[0]['analy']?></span>" 입니다.</h3><hr id="gray_hr">
            <div class="table-responsive" style="border:none;">
            <table class="table" id="summary_table">
              <thead>
                <tr style="text-align:center">
                  <th>평균 기여 처치</th>
                  <th>평균 기여 시간</th>
                  <th>평균 딜</th>
                  <th>평균 죽음</th>
                  <th>평균 처치</th>
                  <th>평균 치유량</th>
                  <th>평균 솔로킬</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><span id="avg_contributions_kill"><?=$summary[0]['avg_contributions_kill']?></span></td>
                  <td><span id="avg_contributions_time"><?=$summary[0]['avg_contributions_time']?></span></td>
                  <td><span id="avg_damage"><?=$summary[0]['avg_damage']?></span></td>
                  <td><span id="avg_death"><?=$summary[0]['avg_death']?></span></td>
                  <td><span id="avg_kill"><?=$summary[0]['avg_kill']?></span></td>
                  <td><span id="avg_heal"><?=$summary[0]['avg_heal']?></span></td>
                  <td><span id="avg_solo_kill"><?=$summary[0]['avg_solo_kill']?></span></td>
                </tr>
              </tbody>
            </table>
            </div>
            <hr id="gray_hr">
            <h3 style="text-align:center">영웅 픽순위</h3>
            <div class="table-responsive" style="border:none;">
            <table class="table" id="frequency_table">
              <thead>
                <tr style="text-align:center">
                  <th>영웅</th>
                  <th>픽순위</th>
                  <th>승리</th>
                  <th>승률</th>
                  <th>킬/데스</th>
                  <th>영웅 플레이 시간</th>
                </tr>
              </thead>
              <tbody id="frequency_inner_data">
                <?php
                foreach ($frequency as $key => $value) {
                    echo "<tr>";
                    echo "<td><a class=\"none_a\">".$value["hero"]."</td>";
                    echo "<td>".$value["freq_index"]."</td>";
                    echo "<td>".$value["win"]."</td>";
                    echo "<td>".$value["outcome"]."</td>";
                    echo "<td>".$value["K/D"]."</td>";
                    echo "<td>".$value["time"]."</td>";
                    echo "</tr>";
                }
                ?>
              </tbody>
            </table>
            </div>
            <hr id="gray_hr">
            <div id="heroes_inner_data">
                <?php
                foreach ($heroes as $key => $value) {
                    $hero[$value["hero"]][$value["category"]][] = $value["title"];
                    $hero[$value["hero"]][$value["category"]][] = $value["value"];
                }
                $flag = true;
                foreach ($hero as $type => $type_value) {
                    echo "<hr id=\"item_hr\"><h3 id=\"".$type."\"style=\"color:white;\"><a class=\"none_a\" href=\"#top\">".$type."</a></h3><br>";
                    foreach ($type_value as $key => $detail) {
                        echo "<h4 style=\"margin-left:3%;text-align:left;font-weight: bold;\">".$key."</h4><div class=\"contents\">";
                        foreach ($detail as $key => $value) {
                            if ($flag)
                            {
                                echo "<div class=\"item\"><div class=\"title\">".$value."</div>";
                                $flag = false;
                            }
                            else
                            {
                                echo "<div class=\"spec\">".$value."</div></div>";
                                $flag = true;
                            }
                        }
                        echo "</div>";
                    }
                }
                ?>
            </div>
          </div>
        </div>
    </div>
    <div class="col-md-2"></div>
  </div>
  <script src="/public/js/oversearch.js" charset="utf-8"></script>