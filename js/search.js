const search = (colIndexesArr, searchTerm) =>{
    table.columns(colIndexesArr).search(searchTerm).draw();
};