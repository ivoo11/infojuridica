(() => {
    const list=document.getElementById('eventDays');
    const add=document.getElementById('addEventDay');
    if (!list || !add) return;
    function renumber() {
        [...list.children].forEach((row,index)=>{
            row.querySelectorAll('input').forEach(input=>{ input.name=input.name.replace(/days\[\d+\]/, `days[${index}]`); });
            row.querySelector('[data-remove-day]').disabled=list.children.length===1;
        });
        add.disabled=list.children.length>=60;
    }
    add.addEventListener('click',()=>{
        if (list.children.length>=60) return;
        const row=list.firstElementChild.cloneNode(true);
        row.querySelector('[type="date"]').value='';
        // Reuse the prior day's times; the editor can change them independently.
        const last=list.lastElementChild;
        [...row.querySelectorAll('[type="time"]')].forEach((input,index)=>input.value=last.querySelectorAll('[type="time"]')[index].value);
        list.append(row);renumber();row.querySelector('input').focus();
    });
    list.addEventListener('click',event=>{
        const remove=event.target.closest('[data-remove-day]');
        if (!remove || list.children.length===1) return;
        remove.closest('.event-day-row').remove();renumber();add.focus();
    });
    renumber();
})();
