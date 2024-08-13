function openSpinner(el){
    const cont = !el ? 'loaderContainer' : el;
    const lc = document.getElementById(cont);
    lc.classList.remove('d-none');
  }
  function closeSpinner(el){
     const cont = !el ? 'loaderContainer' : el;
     const lc = document.getElementById(cont);
    lc.classList.add('d-none');
  }