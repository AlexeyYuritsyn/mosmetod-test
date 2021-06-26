function uploads_file_material_log(url_file_material,material_guid) {

    var xhr = new XMLHttpRequest();

    let url = new URL(location.origin + '/materials/uploads-file-material-log');
    url.searchParams.set('url_file_material', url_file_material);
    url.searchParams.set('material_guid', material_guid);

    xhr.open('POST', url, false);

    xhr.send();

    if (xhr.status != 200) {
        alert( xhr.status + ': ' + xhr.statusText ); // пример вывода: 404: Not Found
    }

    return false;
}