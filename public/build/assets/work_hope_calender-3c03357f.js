import{C as d,i as c,a as r}from"./index-73320e29.js";import{i as u}from"./index-3666857a.js";var h=document.getElementById("work_hope_calendar"),n=new Date,o=new Date(n);o.setMonth(n.getMonth()+1);let l=new d(h,{schedulerLicenseKey:"CC-Attribution-NonCommercial-NoDerivatives",plugins:[c,u],initialView:"resourceTimelineMonth",initialDate:o,headerToolbar:{left:"",center:"title",right:""},locale:"ja",contentHeight:"auto",editable:!0,resourceAreaHeaderContent:"従業員",resources:[],events:function(t,s,v){console.log(t),r.post("work/all-member-get",{display_start_day:t.start.valueOf(),display_status:"hope"}).then(e=>{var a=e.data,i=a;console.log(e),l.setOption("resources",i)}).catch(e=>{alert("登録に失敗しました")}),r.post("work/all-shift-get",{start_date:t.start.valueOf(),end_date:t.end.valueOf()}).then(e=>{var a=e.data;s(a)}).catch(e=>{alert("登録に失敗しました")})}});l.render();