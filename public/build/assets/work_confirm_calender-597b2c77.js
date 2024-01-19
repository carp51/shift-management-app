import{a as n,C as f,i as h}from"./index-73320e29.js";import{i as m}from"./index-3666857a.js";var v=document.getElementById("work_confirm_calendar"),c=new Date,d=new Date(c);d.setMonth(c.getMonth()+1);var s,l,i=0;document.getElementById("shiftTempCreate").addEventListener("click",function(e){confirm("従業員の希望シフトを従業員に公開するシフトに上書きしますか？")&&n.post("/user/work/shift-temp-create",{start_date:s,end_date:l}).then(()=>{document.location.reload()}).catch(()=>{alert("登録に失敗しました")})});document.getElementById("shiftShow").addEventListener("click",function(e){if(i)var t="シフトの公開を取り消しますか？";else var t="シフトを公開しますか";confirm(t)&&n.post("/user/work/confirm/shift-show",{display_start_date:s}).then(a=>{var r=a.data;r==1?document.getElementById("shiftShow").innerText="シフトの公開を取り消す":document.getElementById("shiftShow").innerText="シフトを公開する"}).catch(()=>{alert("登録に失敗しました")})});document.getElementById("shiftToExcel").addEventListener("click",function(e){console.log(s),console.log(l.valueOf()),n.post("/user/work/confirm/excel-file-get",{display_start_date:s,display_end_date:l},{responseType:"blob"}).then(t=>{console.log(t),saveAs(t.data,"myExcelFile.xlsx")}).catch(()=>{alert("エクセルを出力するのに失敗しました")})});let o=new f(v,{schedulerLicenseKey:"CC-Attribution-NonCommercial-NoDerivatives",plugins:[h,m],initialView:"resourceTimelineMonth",initialDate:d,headerToolbar:{left:"prev",center:"title",right:"next"},locale:"ja",contentHeight:"auto",editable:!0,resourceAreaHeaderContent:"従業員",resources:[],selectable:!0,select:function(e){var t=e.start.toISOString().slice(0,10),a=new Date(e.end.getTime()-24*60*60*1e3).toISOString().slice(0,10);if(t!==a){alert("一日だけ選択してください");return}var r=null;$("#exampleModal").modal("show").on("click",function(){$(".early-shift-btn, .late-shift-btn, .fulltime-shift-btn").off("click")}),$(".early-shift-btn, .late-shift-btn, .fulltime-shift-btn").on("click",function(){r=$(this).text(),r&&n.post("/user/work/confirm/shift-add",{start_date:e.start.valueOf(),end_date:e.end.valueOf(),shift_type:r,user_id:e.resource._resource.id}).then(()=>{console.log(e.start),o.addEvent({title:r,start:e.start,end:e.end,resourceId:e.resource._resource.id,allDay:!0})}).catch(()=>{alert("登録に失敗しました")}),$("#exampleModal").hide(),$(".early-shift-btn, .late-shift-btn, .fulltime-shift-btn").off("click")})},events:function(e,t){s=e.start.valueOf(),l=e.end.valueOf(),n.post("work/all-member-get",{display_start_day:e.start.valueOf(),display_status:"confirm"}).then(a=>{var r=a.data,u=r;o.setOption("resources",u)}).catch(()=>{alert("登録に失敗しました")}),n.post("work/confirm/all-shift-get",{start_date:e.start.valueOf(),end_date:e.end.valueOf()}).then(a=>{t(a.data)}).catch(()=>{alert("登録に失敗しました")}),n.post("work/confirm/shift-show-status-get",{start_date:e.start.valueOf()}).then(a=>{a.data?(document.getElementById("shiftShow").innerText="シフトの公開を取り消す",i=1):(document.getElementById("shiftShow").innerText="シフトを公開する",i=0)}).catch(()=>{alert("登録に失敗しました")})},eventClick:function(e){confirm("削除しますか？")&&(n.post("work/confirm/shift-delete",{start_date:e.event._instance.range.start.valueOf(),end_date:e.event._instance.range.end.valueOf(),user_id:e.event._def.resourceIds}).then(()=>{}).catch(()=>{alert("削除に失敗しました")}),e.event.remove())}});o.render();