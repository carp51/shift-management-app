import{a as l,C as f,i as h}from"./index-73320e29.js";import{i as _}from"./index-7ae24b1e.js";var p=document.getElementById("shift_planning_calendar"),u=new Date,i=new Date(u);i.setMonth(u.getMonth()+1);var t=0;const g=document.getElementById("plus-btn"),k=document.getElementById("bulk-modal-plus-btn");g.addEventListener("click",function(){t=parseInt(document.getElementById("counter-input").value,10)+1,document.getElementById("counter-input").value=t});k.addEventListener("click",function(){t=parseInt(document.getElementById("bulk-modal-counter-input").value,10)+1,document.getElementById("bulk-modal-counter-input").value=t});const y=document.getElementById("minus-btn"),E=document.getElementById("bulk-modal-minus-btn");y.addEventListener("click",function(){t-1>=0&&(t=parseInt(document.getElementById("counter-input").value,10)-1,document.getElementById("counter-input").value=t)});E.addEventListener("click",function(){t-1>=0&&(t=parseInt(document.getElementById("bulk-modal-counter-input").value,10)-1,document.getElementById("bulk-modal-counter-input").value=t)});var r,o,s,m,v;document.getElementById("counter-confirm").addEventListener("click",function(e){l.post("shift-planning/shift-planning-edit",{start_date:o,end_date:s,title:r,need_number:t}).then(()=>{document.location.reload()}).catch(()=>{alert("変更に失敗しました")})});document.getElementById("bulkSelectDecision").addEventListener("click",function(){var e=document.getElementsByClassName("day-of-week-checks"),a=[];for(let n=0;n<7;n++)e[n].checked===!0&&a.push(e[n].value);var d=document.getElementsByName("shift-checks"),c="";for(let n=0;n<2;n++)d[n].checked===!0&&(c=d[n].value);if(!(a.length!=0&&c!="")){alert("曜日とシフトを選択してください");return}l.post("shift-planning/shift-planning-bulk-edit",{display_start_date:m,display_end_date:v,shift_checked:c,day_of_week_checked_list:a,need_number:t}).then(()=>{document.location.reload()}).catch(()=>{alert("変更に失敗しました")})});let B=new f(p,{plugins:[_,h],initialView:"dayGridMonth",initialDate:i,headerToolbar:{left:"",center:"title",right:""},locale:"ja",events:function(e,a){m=e.start.valueOf(),v=e.end.valueOf(),l.post("shift-planning/shift-planning-add",{start_date:e.start.valueOf(),end_date:e.end.valueOf()}).then(()=>{}).catch(()=>{alert("登録に失敗しました")}),l.post("shift-planning/shift-planning-get",{start_date:e.start.valueOf(),end_date:e.end.valueOf()}).then(d=>{a(d.data)}).catch(()=>{alert("取得に失敗しました")})},eventClick:function(e){o=e.event._instance.range.start.valueOf(),s=e.event._instance.range.end.valueOf(),r=e.event._def.title;var a=e.event._def.title;t=parseInt(a.substr(a.indexOf("_")+1),10),document.getElementById("counter-input").value=t,$("#counter-modal").modal("show").off("click",".btn-secondary")}});B.render();
