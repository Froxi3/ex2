BX.ready(function () {
    let ajax_report = arParams['AJAX_REPORT_NEWS'];
    let reportLink = document.getElementById('report-link');

    let reportResult = BX.create({
        tag: 'span',
        props:
        {
            id: 'report-result'
        }, 
    });
    
    BX.insertAfter(reportResult, reportLink);

    if (ajax_report == 'Y') {
        reportLink.addEventListener('click', event => {
            event.preventDefault();

            BX.ajax.loadJSON(
                reportLink.href.split(/[?#]/)[0],
                {
                    'MODE_AJAX': 'true',
                    'ID': arParams['ID']
                },
                function (data) {
                    reportResult.innerText = "Ваше мнение учтено, №" + data['ID'];
                },
                function (data) {
                    reportResult.innerText = "Ошибка!";
                }
            );
        })
    }
    else {
        reportLink.href = window.location.href.split(/[?#]/)[0] + '?MODE_AJAX=false&ID=' + arParams['ID'];
        let params = new URLSearchParams(document.location.search);

        if (params.get('RESULT')) {
            let idCreateNews = params.get('ID');

            if (idCreateNews) {
                reportResult.innerText = "Ваше мнение учтено, №" + idCreateNews;
            }
            else {
                reportResult.innerText = "Ошибка!";
            }
        }
    }
});
