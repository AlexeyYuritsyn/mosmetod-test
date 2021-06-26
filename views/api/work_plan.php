<h3 style="text-align: center"><?=$title?></h3>

<?php if(!empty($content)): ?>
    <table border="1" style="width: 100%; border: 4px double black; border-collapse: collapse; " cellspacing="2" cellpadding="5">
        <tr>
            <th>Предмет/направление</th>
            <th>Для кого</th>
            <th>Дата</th>
            <th>Время</th>
            <th>Мероприятие</th>
            <th>Округ</th>
            <th>Место проведения</th>
            <th>Ответственный</th>
            <th>Примечание</th>
            <th>Ссылки</th>
        </tr>

        <?php foreach($content as $value): ?>
            <?php //var_dump($content); ?>
            <tr>
                <td><?=$value['subject']?></td>
                <td><?=$value['for_whom']?></td>
                <td><?=$value['period']?></td>
                <td><?=$value['event_time']?></td>
                <td><?=$value['desc']?></td>
                <td><?=$value['district']?></td>
                <td><?=$value['place']?></td>
                <td><?=$value['admin']?></td>
                <td><?=$value['description']?></td>
                <td>
                    <?php if(!empty($value['links'])): ?>
                        <?php foreach($value['links'] as $val): ?>
                            <a href="<?=$val['url']?>"><?=$val['title']?></a><br><br>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

