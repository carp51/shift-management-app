<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>シフト確定画面</title>
    @vite(['resources/css/app.css', 'resources/js/shift_view_calender.js'])
</head>

<body>
    @include("components.header")

    <div class="accordion" id="accordionSalary">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                    aria-expanded="true" aria-controls="collapseOne">
                    あなたの表示月の月収
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <p id="user-salary-text">あなたの表示月の月収は0円です</p>
                </div>
            </div>
        </div>
    </div>

    <h3 style="text-align: center; margin-top: 30px;"></h3>
    <div id='shift_view_calendar'></div>
</body>

</html>