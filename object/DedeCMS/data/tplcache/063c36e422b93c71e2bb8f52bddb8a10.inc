<li> <a href="{dede:field name='typeurl'/}" class="nav-link nav-drop {dede:field.currentstyle/}">{dede:field name='typename'/}</a>
              <div class="classify">
                
                <div class="classify-box">
                  <div class="classify-ti"> <img src="/skin/img/c-icon3.png" alt="装修套餐" /> <span>装修套餐</span> </div>
                  <div class="classify-list"> {dede:php} AddFilter(2,1,'jz');{/dede:php} </div>
                </div>
                
                <div class="classify-box">
                  <div class="classify-ti"> <img src="/skin/img/c-icon2.png" alt="设计风格" /> <span>设计风格</span> </div>
                  <div class="classify-list"> {dede:php} AddFilter(2,1,'fg');{/dede:php} </div>
                </div>
                
                <div class="classify-box">
                  <div class="classify-ti"> <img src="/skin/img/c-icon3.png" alt="装修户型" /> <span>装修户型</span> </div>
                  <div class="classify-list"> {dede:php} AddFilter(2,1,'hx');{/dede:php} </div>
                </div>
                
                <div class="classify-box">
                  <div class="classify-ti"> <img src="/skin/img/c-icon4.png" alt="装修面积" /> <span>装修面积</span> </div>
                  <div class="classify-list">{dede:php} AddFilter(2,1,'mj');{/dede:php}</div>
                </div>
              </div>
            </li>