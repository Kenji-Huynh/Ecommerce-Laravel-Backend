<?php $__env->startSection('title', 'Đơn hàng'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Quản lý đơn hàng</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($order->id); ?></td>
                            <td><span class="badge bg-secondary"><?php echo e($order->order_number); ?></span></td>
                            <td>
                                <?php if($order->user): ?>
                                    <?php echo e($order->user->name); ?><br>
                                    <small class="text-muted"><?php echo e($order->user->email); ?></small>
                                <?php else: ?>
                                    <em>Guest</em>
                                <?php endif; ?>
                            </td>
                            <td>$<?php echo e(number_format($order->total, 2)); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e(['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','canceled'=>'dark'][$order->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger')); ?>">
                                    <?php echo e(ucfirst($order->payment_status)); ?>

                                </span>
                            </td>
                            <td><?php echo e($order->created_at->format('Y-m-d H:i')); ?></td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-sm btn-outline-primary" title="Xem/Sửa">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.orders.destroy', $order->id)); ?>" method="POST" onsubmit="return confirm('Xóa đơn hàng này?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Xóa">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center p-4">Chưa có đơn hàng</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($orders->hasPages()): ?>
        <div class="card-footer"><?php echo e($orders->links()); ?></div>
    <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\InternProjectReact\Ecommerce\backend\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>