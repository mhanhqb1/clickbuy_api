<html>
    <head>
        <title>LyonaBeauty</title>
        <style>
            td {
                border: 1px solid #ccc;
            }
        </style>
    </head>
    <body>
        <h3><?php echo $name.' - '.$phone.' - ';?></h3>
        <h4>Địa chỉ: <?php echo !empty($address) ? $address : ''; ?></h4>
        <p>Ghi chú: <?php !empty($note) ? $note : ''; ?></p>
        <table>
            <thead>
                <th>Hình ảnh</th>
                <th>Tên</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th></th>
            </thead>
            <?php if (!empty($detail)): ?>
            <?php
            $total = 0;
            ?>
            <?php foreach ($detail as $v): ?>
            <?php 
            $subTotal = $v['price']*$v['qty'];
            $total += $subTotal;
            ?>
            <tr>
                <td><img src="<?php echo $v['image'];?>" width="100px" height="100px"/></td>
                <td><?php echo $v['name'];?></td>
                <td><?php echo $v['qty'];?></td>
                <td><?php echo number_format($v['price']);?></td>
                <td><?php echo number_format($subTotal);?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4">Tổng tiền</td>
                <td><?php echo number_format($total);?></td>
            </tr>
            <?php endif; ?>
        </table>
    </body>
</html>