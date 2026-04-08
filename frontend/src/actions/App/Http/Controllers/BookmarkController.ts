import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BookmarkController::index
* @see app/Http/Controllers/BookmarkController.php:13
* @route '/bookmarks'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/bookmarks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BookmarkController::index
* @see app/Http/Controllers/BookmarkController.php:13
* @route '/bookmarks'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BookmarkController::index
* @see app/Http/Controllers/BookmarkController.php:13
* @route '/bookmarks'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BookmarkController::index
* @see app/Http/Controllers/BookmarkController.php:13
* @route '/bookmarks'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BookmarkController::index
* @see app/Http/Controllers/BookmarkController.php:13
* @route '/bookmarks'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BookmarkController::index
* @see app/Http/Controllers/BookmarkController.php:13
* @route '/bookmarks'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BookmarkController::index
* @see app/Http/Controllers/BookmarkController.php:13
* @route '/bookmarks'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\BookmarkController::toggle
* @see app/Http/Controllers/BookmarkController.php:31
* @route '/bookmarks/{product}'
*/
export const toggle = (args: { product: string | number | { id: string | number } } | [product: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/bookmarks/{product}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BookmarkController::toggle
* @see app/Http/Controllers/BookmarkController.php:31
* @route '/bookmarks/{product}'
*/
toggle.url = (args: { product: string | number | { id: string | number } } | [product: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { product: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { product: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            product: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        product: typeof args.product === 'object'
        ? args.product.id
        : args.product,
    }

    return toggle.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BookmarkController::toggle
* @see app/Http/Controllers/BookmarkController.php:31
* @route '/bookmarks/{product}'
*/
toggle.post = (args: { product: string | number | { id: string | number } } | [product: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BookmarkController::toggle
* @see app/Http/Controllers/BookmarkController.php:31
* @route '/bookmarks/{product}'
*/
const toggleForm = (args: { product: string | number | { id: string | number } } | [product: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BookmarkController::toggle
* @see app/Http/Controllers/BookmarkController.php:31
* @route '/bookmarks/{product}'
*/
toggleForm.post = (args: { product: string | number | { id: string | number } } | [product: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, options),
    method: 'post',
})

toggle.form = toggleForm

const BookmarkController = { index, toggle }

export default BookmarkController