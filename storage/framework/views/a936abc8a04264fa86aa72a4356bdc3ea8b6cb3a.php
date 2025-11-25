<?php $__env->startSection('title', 'Quản lý sản phẩm'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-box me-2"></i>Danh sách sản phẩm</h2>
            <p class="text-muted mb-0">Quản lý tất cả sản phẩm của cửa hàng</p>
        </div>
        <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary btn-lg">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
        </a>
    </div>

    <!-- Alert Success -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Products Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Tổng số: <span class="badge bg-primary"><?php echo e($products->total()); ?></span> sản phẩm
                    </h5>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th class="text-center" style="width: 80px;">Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th class="text-end">Giá</th>
                            <th class="text-center">Kho</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center fw-bold">#<?php echo e($product->id); ?></td>
                                <td class="text-center">
                                    <?php if($product->main_image): ?>
                                        <img src="<?php echo e($product->main_image); ?>" 
                                             alt="<?php echo e($product->name); ?>" 
                                             class="rounded shadow-sm"
                                             width="60" 
                                             height="60" 
                                             style="object-fit: cover;" 
                                             onerror="this.src='/vite.svg'">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo e($product->name); ?></strong>
                                    <?php if($product->description): ?>
                                        <br><small class="text-muted"><?php echo e(Str::limit($product->description, 50)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo e($product->category->name ?? 'Không có'); ?>

                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">$<?php echo e(number_format($product->price, 2)); ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php if($product->stock_quantity > 10): ?>
                                        <span class="badge bg-success"><?php echo e($product->stock_quantity); ?></span>
                                    <?php elseif($product->stock_quantity > 0): ?>
                                        <span class="badge bg-warning text-dark"><?php echo e($product->stock_quantity); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($product->in_stock): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Còn hàng
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Hết hàng
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('admin.products.show', $product->id)); ?>" 
                                           class="btn btn-sm btn-info" 
                                           title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" 
                                           class="btn btn-sm btn-primary"
                                           title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.products.destroy', $product->id)); ?>" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Chưa có sản phẩm nào</p>
                                    <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>Thêm sản phẩm đầu tiên
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if($products->hasPages()): ?>
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị <strong><?php echo e($products->firstItem()); ?></strong> 
                    đến <strong><?php echo e($products->lastItem()); ?></strong> 
                    trong tổng số <strong><?php echo e($products->total()); ?></strong> sản phẩm
                </div>
                <div>
                    <?php echo e($products->links()); ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\InternProjectReact\Ecommerce\backend\resources\views/admin/products/index.blade.php ENDPATH**/ ?>