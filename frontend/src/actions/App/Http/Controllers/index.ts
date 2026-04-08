import ReelFeedController from './ReelFeedController'
import SearchController from './SearchController'
import CategoryController from './CategoryController'
import ProductController from './ProductController'
import BookmarkController from './BookmarkController'
import CartController from './CartController'
import OrderController from './OrderController'
import Settings from './Settings'

const Controllers = {
    ReelFeedController: Object.assign(ReelFeedController, ReelFeedController),
    SearchController: Object.assign(SearchController, SearchController),
    CategoryController: Object.assign(CategoryController, CategoryController),
    ProductController: Object.assign(ProductController, ProductController),
    BookmarkController: Object.assign(BookmarkController, BookmarkController),
    CartController: Object.assign(CartController, CartController),
    OrderController: Object.assign(OrderController, OrderController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers