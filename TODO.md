# TODO: Implement Organization-Based Data Isolation

## Tasks
- [x] Update ManagementStockController.php index method to filter Reagen data by user's organization_id
- [x] Update other methods in ManagementStockController.php that fetch Reagen data (e.g., viewReagen, editReagen, etc.) to include organization_id filter
- [x] Check and update other controllers if needed (e.g., LogbookController, OrderController, etc.) to ensure data isolation
- [ ] Test the changes by logging in as users from different organizations
- [ ] Add organization_id to Reagen model fillable if not present (based on migration)
- [ ] Update related models (ReagenIn, StockReagen, etc.) to include organization_id filtering if applicable
